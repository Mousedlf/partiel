<?php

namespace App\Repository;

use App\Entity\PrivateConversation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PrivateConversation>
 *
 * @method PrivateConversation|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrivateConversation|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrivateConversation[]    findAll()
 * @method PrivateConversation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrivateConversationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrivateConversation::class);
    }

//    /**
//     * @return PrivateConversation[] Returns an array of PrivateConversation objects
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

//    public function findOneBySomeField($value): ?PrivateConversation
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
