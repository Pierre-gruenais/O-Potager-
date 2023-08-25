<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;


class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event): void
    {
    
       
        // je récupère le requête 
        $request = $event->getRequest();
        // si ma route ne commence pas par api, je fais un early return
        if(strpos($request->getPathInfo(),"/api/")!== 0){
            // ceci est un early return ça permet de couper l'execution de la fonction
            return;
        }

        //  récupérer l'exception
        $exception = $event->getThrowable();
        // le cas des erreurs serveur
        if($exception->getCode() === 0){
            return;
        }
        //  renvoyer une réponse avec en JSON le contenu du message ET le bon code http
        $response = new JsonResponse(
            ["error" => $exception->getMessage()],
            $exception->getStatusCode()
            
        );
        //! pour composer require symfony/http-kernel 
        // ici je set la nouvelle réponse qui est personnalisé
        $event->setResponse($response);
        

    }

    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.exception' => 'onKernelException',
        ];
    }
}