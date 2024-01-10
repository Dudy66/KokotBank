<?php

namespace App\Repository;

use App\Entity\CompteAction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CompteAction>
 *
 * @method CompteAction|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompteAction|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompteAction[]    findAll()
 * @method CompteAction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompteActionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompteAction::class);
    }

//    /**
//     * @return CompteAction[] Returns an array of CompteAction objects
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

//    public function findOneBySomeField($value): ?CompteAction
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
