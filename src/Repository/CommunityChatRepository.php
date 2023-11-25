<?php

namespace App\Repository;

use App\Entity\CommunityChat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CommunityChat>
 *
 * @method CommunityChat|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommunityChat|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommunityChat[]    findAll()
 * @method CommunityChat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommunityChatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommunityChat::class);
    }

//    /**
//     * @return CommunityChat[] Returns an array of CommunityChat objects
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

//    public function findOneBySomeField($value): ?CommunityChat
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
