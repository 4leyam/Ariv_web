<?php

namespace App\Repository;

use App\Entity\InvitationTokens;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method InvitationTokens|null find($id, $lockMode = null, $lockVersion = null)
 * @method InvitationTokens|null findOneBy(array $criteria, array $orderBy = null)
 * @method InvitationTokens[]    findAll()
 * @method InvitationTokens[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvitationTokensRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, InvitationTokens::class);
    }

//    /**
//     * @return InvitationTokens[] Returns an array of InvitationTokens objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?InvitationTokens
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
