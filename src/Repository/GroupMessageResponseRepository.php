<?php

namespace App\Repository;

use App\Entity\GroupMessageResponse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GroupMessageResponse>
 *
 * @method GroupMessageResponse|null find($id, $lockMode = null, $lockVersion = null)
 * @method GroupMessageResponse|null findOneBy(array $criteria, array $orderBy = null)
 * @method GroupMessageResponse[]    findAll()
 * @method GroupMessageResponse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupMessageResponseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupMessageResponse::class);
    }

//    /**
//     * @return GroupMessageResponse[] Returns an array of GroupMessageResponse objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?GroupMessageResponse
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
