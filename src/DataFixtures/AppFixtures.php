<?php



namespace App\DataFixtures;

use App\Entity\Favorite;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Garden;
use DateTimeImmutable;
use App\Entity\Picture;
use Ottaviano\Faker\Gravatar;
use App\Service\UnsplashApiService;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\DataFixtures\Provider\AppProvider;

class AppFixtures extends Fixture
{


    private $unsplashApi;

    public function __construct(UnsplashApiService $unsplashApi)
    {
        $this->unsplashApi = $unsplashApi;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create("fr_FR");
        // utilisation de library gravatar pour les avatars
        $faker->addProvider(new Gravatar($faker));
        // utilisation de notre provider pour les roles
        $faker->addProvider(new AppProvider());

        //! USER

        // Je crée un tableau vide
        $userList = [];
        for ($i = 0; $i < 7; $i++) {
            // j'utilise mon provider pour récupérer un $faker->
            $role = $faker->role();
            // J'instancie un nouvel objet user
            $user = new User();
            $user->setUsername($faker->userName());
            $user->setPassword($faker->password(8, 20));
            $user->setEmail($faker->email());
            $user->setPhone($faker->phoneNumber());
            $user->setRole($role);
            $user->setAvatar($faker->gravatarUrl());
            $user->setCreatedAt(new DateTimeImmutable($faker->date()));

            $userList[] = $user;

            $manager->persist($user);

        }

        // ! Garden

        // Je crée un tableau vide
        $gardenList = [];
        for ($i = 0; $i < 20; $i++) {
            // J'instancie un nouvel objet garden
            $garden = new Garden();
            $garden->setTitle($faker->text(100));
            $garden->setDescription($faker->text(240));
            $garden->setAddress($faker->streetAddress());
            $garden->setPostalCode($faker->numberBetween(1000, 95000));
            $garden->setCity($faker->city());
            $garden->setWater($faker->boolean());
            $garden->setTool($faker->boolean());
            $garden->setShed($faker->boolean());
            $garden->setCultivation($faker->boolean());
            $garden->setState($faker->text(10));
            $garden->setSurface($faker->numberBetween(1, 1000));
            $garden->setPhoneAccess($faker->boolean());
            $garden->setCreatedAt(new DateTimeImmutable($faker->date()));
            $garden->setUser($userList[array_rand($userList)]);

            $gardenList[] = $garden;

            $manager->persist($garden);
        }

        //! Favorite

        for ($i = 0; $i < 20; $i++) {
            $favorite = new Favorite();
            $favorite->setUser($userList[array_rand($userList)]);
            $favorite->setGarden($gardenList[array_rand($gardenList)]);

            $manager->persist($favorite);
        }


        // ! Picture
        for ($i = 0; $i < 5; $i++) {
            // J'instancie un nouvel objet picture

            $picture = new Picture();
            // utilisation de l'api Unsplash pour generer des photos de garden
            $picture->setName($this->unsplashApi->fetchPhotosRandom("garden"));
            $picture->setCreatedAt(new DateTimeImmutable($faker->date()));
            $picture->setGarden($gardenList[array_rand($gardenList)]);

            $manager->persist($picture);
        }




        // J'execute les requetes sql
        $manager->flush();
    }
}