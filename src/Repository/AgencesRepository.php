<?php

namespace App\Repository;

use App\Entity\Agences;
use App\Entity\NzelaUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Agences|null find($id, $lockMode = null, $lockVersion = null)
 * @method Agences|null findOneBy(array $criteria, array $orderBy = null)
 * @method Agences[]    findAll()
 * @method Agences[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AgencesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Agences::class);
    }

   /**
     * @return Agences[] Returns an array of Agences objects
     */

    public function findTop() {
        return $this->createQueryBuilder('a')
            ->andWhere('a.avis > :val')
            ->setParameter('val', 3)
            ->orderBy('a.avis', 'DESC')
            ->setMaxResults(20)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findRecents() {
        return $this->createQueryBuilder('a')
            ->orderBy('a.id', 'DESC')
            ->setMaxResults(20)
            ->getQuery()
            ->getResult()
            ;
    }

    public function getFindRecentBuilder(?bool $isAgenceAdmin , ?NzelaUser $user ) {
        if((!is_null($isAgenceAdmin) and !is_null($user)) and $isAgenceAdmin) {
            return $this->createQueryBuilder('a')
                ->andWhere('a.id = :id')
                ->setParameter('id' , $user->getIdAgence()->getId());
        } else
        return $this->createQueryBuilder('a')
            ->orderBy('a.id', 'DESC')
            ->setMaxResults(20);
    }

    public function findOlds() {
        return $this->createQueryBuilder('a')
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(20)
            ->getQuery()
            ->getResult()
            ;
    }




    /*
    public function findOneBySomeField($value): ?Agences
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
