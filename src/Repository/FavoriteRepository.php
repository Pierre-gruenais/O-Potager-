<?php

namespace App\Repository;

use App\Entity\Favorite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Favorite>
 *
 * @method Favorite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Favorite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Favorite[]    findAll()
 * @method Favorite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FavoriteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Favorite::class);
    }

    public function add(Favorite $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Favorite $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    /**
     * on affiche tous les favoris d'un utilisateur
     *
     * @param integer|null $id
     * @return array|null
     */
    public function findAllFavoritesByUserId(?int $id): ?array
    {
        return $this->createQueryBuilder('f')
             ->innerJoin("f.user","u")
             ->where("u.id LIKE :id")
             ->setParameter("id", "%$id%")
             ->getQuery()
             ->getResult()
        ;
    }
    public function findOneFavoritesByUserId(int $id,int $favoriteId): ?array
    {
        return $this->createQueryBuilder('f')
             ->innerJoin("f.user","u")
             ->where("u.id LIKE :id")
             ->andWhere("f.id LIKE :favoriteId")
             ->setParameter("id", "%$id%")
             ->setParameter("favoriteId", "%$favoriteId%")
             ->getQuery()
             ->getResult()
        ;
    }

//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Favorite
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
