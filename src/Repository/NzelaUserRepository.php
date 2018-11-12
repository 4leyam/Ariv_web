<?php

namespace App\Repository;

use App\Controller\AppConstants;
use App\Entity\NzelaUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

/**
 * @method NzelaUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method NzelaUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method NzelaUser[]    findAll()
 * @method NzelaUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NzelaUserRepository extends ServiceEntityRepository implements UserLoaderInterface
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, NzelaUser::class);
    }

    public function loadUserByUsername($emaiId) {
        return $this->createQueryBuilder('u')
            ->where('u.pseudo = :username OR u.emaiId = :emailId')
            ->setParameter('username', $emaiId)
            ->setParameter('emailId', $emaiId)
            ->getQuery()
            ->getOneOrNullResult();

    }

    public function getConcernedUser($role , $agence , $email) {
        $role = array_search($role, AppConstants::ROLES);
        return $this->createQueryBuilder('u')
            ->andWhere("u.role < ?0")
            ->andWhere("u.idAgence = ?1")
            ->andWhere("u.emaiId = ?2")
            ->setParameters(['0'=>$role , "1"=>$agence , "2"=>$email])
            ->orderBy('u.id', 'DESC')
            ->getQuery()->getResult();
    }

    public function getAdminUnderRole($role , $agence) {
        //TODO retirer l'utlisateur courant de la liste recuperee.
        $top = array_search(AppConstants::ROLE_APP_OPERATOR_ADMIN , AppConstants::ROLES);
        return ($role >= $top)
            ?
                $this->createQueryBuilder('u')
                ->andWhere("u.role < ?0")
                ->andWhere("u.idAgence = ?1")
                ->setParameters(['0'=>$role , "1"=>$agence])
                ->orderBy('u.id', 'DESC')
                ->getQuery()->getResult()
            : $this->createQueryBuilder('u')
                ->orderBy('u.id', 'DESC')
                ->setMaxResults(40)
                ->getQuery()->getResult()
            ;
    }

//    /**
//     * @return NzelaUser[] Returns an array of NzelaUser objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?NzelaUser
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
