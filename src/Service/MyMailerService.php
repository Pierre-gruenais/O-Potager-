<?php
namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class MyMailerService{

    private $mailer;
    private $sender;
    

    public function __construct(MailerInterface $mailer, string $sender)
    {
        $this->mailer = $mailer;
        $this->sender = $sender;
    }

    /**
     * Send an email with a twig template and mailjet 
     * @param string $subject subject of the email
     * @param string $template  path of twig template
     * @param array $context variables of the template
     * @param string $to person who will received the email
     */
    public function send(string $subject, string $template, array $context, string $to){
       
        $email = (new TemplatedEmail())
        
        ->from($this->sender)
      
        ->to($to)
       
        ->subject($subject)
       
        ->htmlTemplate($template)
        
        ->context($context);

        $this->mailer->send($email);
    }

}