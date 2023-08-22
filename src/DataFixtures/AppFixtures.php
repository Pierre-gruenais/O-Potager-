<?php

namespace App\DataFixtures;


use App\Entity\User;
use App\Entity\Garden;
use App\Service\unsplashApiService;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;


class AppFixtures extends Fixture
{

    
    private $unsplashApi;

    public function __construct(unsplashApiService $unsplashApi)
    {
        $this->unsplashApi = $unsplashApi;
    }

    public function load(ObjectManager $manager): void
    {
        // si je veux mon faker en français, je definis la langue dans le create
        $faker = Factory::create("fr_FR");

        // ! Garden
        // Je crée un tableau vide
        $gardenList = [];
        for ($i = 0; $i < 20; $i++) {
        // J'instancie un nouvel objet garden
            $garden = new Garden();

            $garden->setTitle($faker->);
            $garden->setDescription($faker->);
            $garden->setAddress($faker->);
            $garden->setPostalCode($faker->);
            $garden->setCity($faker->);
            $garden->setWater($faker->);
            $garden->setTool($faker->);
            $garden->setShed($faker->);
            $garden->setCultivation($faker->);
            $garden->setState($faker->);
            $garden->setSurface($faker->);
            $garden->setPhoneAccess($faker->);
            $garden->setCreatedAt($faker->);
            $garden->setUpdatedAt($faker->);
            $garden->setuser_id($faker->);

            $gardenList[] = $gardenList;

            $manager->persist($garden);
        }

        //! USER

        for ($i = 0; $i < 7; $i++) {
            // j'utilise mon provider pour récupérer un $faker->
            $role = $faker->role();
            // J'instancie un nouvel objet user
            $user = new User();
           
            $user->setUsername($faker->);
            $user->setPassword($faker->);
            $user->setEmail($faker->);
            $user->setPhone($faker->);
            $user->setRole($role);
            $user->setAvatar($faker->);
            $user->setCreatedAt($faker->);
            $user->setUpdatedAt($faker->);

            $manager->persist($user);
                
       // ! Picture

            // J'instancie un nouvel objet picture
            $picture = new Picture();

        // J'execute les requetes sql
        $manager->flush();
    }
}
