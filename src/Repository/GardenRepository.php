<?php

namespace App\Repository;

use App\Entity\Garden;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Garden>
 *
 * @method Garden|null find($id, $lockMode = null, $lockVersion = null)
 * @method Garden|null findOneBy(array $criteria, array $orderBy = null)
 * @method Garden[]    findAll()
 * @method Garden[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GardenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Garden::class);
    }

    public function add(Garden $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Garden $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Filters the results using the checked field
     *
     * @param string $value of field
     * @return array
     */
    public function findByCheckedField(string $value): array
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.checked = :val')
            ->setParameter('val', $value)
            ->orderBy('g.createdAt', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

//    public function findOneBySomeField($value): ?Garden
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    /**
     * method for recovering gardens in relation to a town and its distance
     *
     * @param float $lat city's latitude retrieved from the nominatimApi
     * @param float $lon city's longitude retrieved from the nominatimApi
     * @param integer $distance distance between a city and a garden
     * @return Array [] array gardens
     */
    public function findGardensByCoordonates(float $lat, float $lon, int $distance)
    {
        $formule = "(6366*acos(cos(radians($lat))*cos(radians(`lat`))*cos(radians(`lon`) - radians($lon))+sin(radians($lat))*sin(radians(`lat`))))";

        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT garden.*, user.username, user.email, user.phone, user.avatar,' . $formule .' AS dist
            FROM garden
            INNER JOIN user ON garden.user_id = user.id
            WHERE ' . $formule . '<= :distance 
            ORDER BY dist ASC
            ';

        $resultSet = $conn->executeQuery($sql, ['distance' => $distance]);

        return $resultSet->fetchAllAssociative();
    }
}
