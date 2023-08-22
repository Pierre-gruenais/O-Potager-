<?php

namespace App\DataFixtures;


use App\Entity\User;
use App\Entity\Garden;
use App\Service\unsplashApiService;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class AppFixtures extends Fixture
{

    // ! Ceci est de l'injection de dépandance
    private $passwordHasher;
    private $unsplashApi;

    public function __construct(UserPasswordHasherInterface $passwordHasher, OmdbApiService $unsplashApi)
    {
        $this->passwordHasher = $passwordHasher;
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
            $garden = new Garden();
            $garden->setTitle($faker->);
          

            $gardenList[] = $gardenList;

            $manager->persist($gardenList);
        }


        // ! User

        // Je crée un tableau vide
        $userList = [];

        for ($i = 0; $i < 20; $i++) {
            $genre = new Genre;
            $genre->setName($faker->unique()->genre());
            $manager->persist($genre);

            // j'ajoute le genre dans le tableau pour pouvoir faire l'association plus loin avec un film
            $genreList[] = $genre;
        }
        // ici genreList est rempli de tous mes genres

        // ! MOVIE

        for ($i = 0; $i < 20; $i++) {
            # code...
            $movie = new Movie();
            $movie->setTitle($faker->unique()->movie());
            $movie->setDuration($faker->numberBetween(60, 200));
            $movie->setReleaseDate(new \DateTimeImmutable($faker->date()));
            $movie->setSynopsis($faker->paragraphs(5, true));
            $movie->setSummary($faker->text(100));
            // $imageFromApi = $this->omdbApi->fetchPoster($movie);
            // if($imageFromApi){
            //     $movie->setPoster($imageFromApi);
            // }else{
            $movie->setPoster("https://picsum.photos/id/" . mt_rand(50, 120) . "/768/1024");
            // }

            // aléatoirement faire un film ou une série
            $random = mt_rand(0, 1);
            if ($random) {
                // si c'est une série créer une saison 
                $movie->setType("film");
            } else {
                $movie->setType("série");
                // je crée mes saison
                for ($j = 1; $j <= 5; $j++) {
                    $season = new Season();
                    // je remplis les champs 
                    $season->setNumberEpisode(mt_rand(5, 25));
                    $season->setNumberSeason($j);
                    // je lie la saison au film
                    $movie->addSeason($season);
                    // je persist la saison pour la mettre dans le sac qui sera flush plus tard
                    $manager->persist($season);
                }
            }

            // $movie->setRating($faker->randomFloat(1, 1, 5));

            // j'associe genre et film
            // *liaison film et genre
            // je créer aléatoirement quelques genres

            // je prend 4 indexs aléatoires dans mon tableau de genre
            $randomIndexArray = array_rand($genreList, 4);

            // je boucle pour ajouter aléatoirement 1 à 4 genres
            for ($k = 0; $k < mt_rand(1, 4); $k++) {


                // je lie un genre de mon tableau aléatoire à mon film
                $movie->addGenre($genreList[$randomIndexArray[$k]]);
            }

            // ! CASTING
            // Je créer un tableau avec 10 index
            $creditOrders = range(1, 10);
            // je mélange le tableau pour avoir mes crédits dans un ordre aléatoire
            shuffle($creditOrders);

            for ($l = 0; $l < 10; $l++) {
                // création du casting
                $casting = new Casting();

                // je mets mes données dans le casting
                $casting->setCreditOrder($creditOrders[$l]);

                $casting->setRole($faker->name());

                // * je lie à la personne
                $casting->setPerson($personList[array_rand($personList)]);
                // * je lie le casting au film
                $movie->addCasting($casting);

                // on oublie pas de persister le casting dans la boucle
                $manager->persist($casting);
            }

            // ! REVIEW
            for ($m = 0; $m < mt_rand(0, 5); $m++) {
                $review = new Review();

                $review->setUsername($faker->userName());
                $review->setEmail($faker->email());
                $review->setContent($faker->text());
                $review->setRating(mt_rand(1, 5));
                $review->setWatchedAt(new DateTimeImmutable($faker->date()));

                // 1er argument un tableau de données fournis par le provider
                // 2eme argument le nombre d'élement à founir
                // 3eme argument autorisation des doublons ou non
                $review->setReactions($faker->randomElements($faker->reactions(), mt_rand(1, 5)));

                // * A cause du listener on est obligé de addReview au lieu de setMovie car la methode addReview permet bien d'ajouté la review dans le tableau de reviews de movie et on pourra l'utiliser dans le listener, dans le sens inverse ça ne fonctionne pas
                $movie->addReview($review);

                $manager->persist($review);
            }
            // J'indique à doctrine que movie est un objet qui doit aller en bdd
            $manager->persist($movie);
        }

        //! USER

        for ($i = 0; $i < 3; $i++) {
            // j'utilise mon provider pour récupérer un user unique
            $dataUser = $faker->unique()->user();
            // J'instancie un nouvel obet user
            $admin = new User();
            // Je set l'email, c'est une string simple
            $admin->setEmail($dataUser["email"]);
            // Je set les roles, c'est un tableau
            $admin->setRoles($dataUser["roles"]);
            // Je set le password, c'est une string simple
            // J'utilise le passwordHasher passé au constructeur, ça s'appelle de l'injection de dépendance (on en parlera dans les prochains jours), il contient la méthode hashPassword qui permet de changer une string en hash par rapport à la méthode de hashage définis dans security.yaml
            // ! SI PAS DE HASH, PAS POSSIBLE DE S'AUTHENTIFIER
            // ! DE PLUS UN MDP EN CLAIR EN BDD EST A LA LIMITE DE L'ILLEGALITE
            $admin->setPassword($this->passwordHasher->hashPassword($admin, $dataUser["password"]));

            // On oublis pas de persister l'objet
            $manager->persist($admin);
        }

        // J'execute les requetes sql
        $manager->flush();
    }
}
