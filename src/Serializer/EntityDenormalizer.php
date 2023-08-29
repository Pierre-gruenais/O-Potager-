<?php
// src/Serializer/TopicNormalizer.php
namespace App\Serializer;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
/**
 * while the process of deserialization 
 * make possible for the denormalizerInterface to read entity if exists
 * so that we can link an entity to an object 
 */
class EntityDenormalizer implements DenormalizerInterface
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

   
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
   
        return $this->entityManager->find($type,$data);
    }

  
    public function supportsDenormalization($data, string $type, string $format = null)
    {
        
        if(is_int($data) && strpos($type,"App\Entity") === 0){
            return true;
        }
    }
}