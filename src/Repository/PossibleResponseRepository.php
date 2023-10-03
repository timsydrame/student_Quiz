<?php

namespace App\Repository;

use App\Entity\PossibleResponse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PossibleResponse>
 *
 * @method PossibleResponse|null find($id, $lockMode = null, $lockVersion = null)
 * @method PossibleResponse|null findOneBy(array $criteria, array $orderBy = null)
 * @method PossibleResponse[]    findAll()
 * @method PossibleResponse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PossibleResponseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PossibleResponse::class);
    }

//    /**
//     * @return PossibleResponse[] Returns an array of PossibleResponse objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PossibleResponse
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
