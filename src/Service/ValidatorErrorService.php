<?php

namespace App\Service;

use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidatorErrorService
{
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }
    
    public function returnErrors ($entity) {
        $errors = $this->validator->validate($entity);

        if (count($errors) > 0) {
            
            $dataErrors = [];
            
            foreach ($errors as $error) {
                $dataErrors[$error->getPropertyPath()][] = $error->getMessage();
            }
            
            return $dataErrors;
        }
    }
}