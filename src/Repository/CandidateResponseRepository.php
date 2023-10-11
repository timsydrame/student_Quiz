<?php

namespace App\Repository;

use App\Entity\CandidateResponse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CandidateResponse>
 *
 * @method CandidateResponse|null find($id, $lockMode = null, $lockVersion = null)
 * @method CandidateResponse|null findOneBy(array $criteria, array $orderBy = null)
 * @method CandidateResponse[]    findAll()
 * @method CandidateResponse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CandidateResponseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CandidateResponse::class);
    }

//    /**
//     * @return CandidateResponse[] Returns an array of CandidateResponse objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CandidateResponse
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
