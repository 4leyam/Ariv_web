<?php

namespace App\Repository;

use App\Entity\Agences;
use App\Entity\Departs;
use App\Entity\Location;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\FetchMode;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @method Departs|null find($id, $lockMode = null, $lockVersion = null)
 * @method Departs|null findOneBy(array $criteria, array $orderBy = null)
 * @method Departs[]    findAll()
 * @method Departs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DepartsRepository extends ServiceEntityRepository
{

    public static $mois = ['Janvier' , 'Fevrier' , "Mars" , 'Avril' , 'Mai' , 'Juin' , 'Juillet' ,
            'Aout' , 'Septembre' , 'Octobre' , 'Novembre' , 'Decembre'];

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Departs::class);
    }

    public function desableDepart(int $departId) {
        $this->createQueryBuilder('d')
            ->update('d')
            ->set("valide" , "0")
            ->where("d.id = :departId")
            ->setParameter("departId" , $departId)
            ->getQuery();
    }

    public function getFindRecentBuilder(?Agences $agence) {
        if(!is_null($agence)) {
            return $this->createQueryBuilder('d')
                ->andWhere("d.agence = :agence")
                ->setParameter('agence' , $agence)
                ->orderBy('d.id', 'ASC')
                ->setMaxResults(30);
        }
    }

    public function getDepartPeriode(Agences $agence):array {
        $id_agence = $agence->getId();
        //on recupere dab l'annee actuelle

        $filter = null;

        $result = $this->findBy(['agence' => $agence] , ['dateDepart' => 'ASC'] , ['limit' => 1]);

       if(sizeof($result) != 0) {
        /**
         * @var $result Departs
         */
            $result = $result[0];
            //la date du plus ancien depart de l'agence va nous permettre de definir le debut de notre regroupement.
            $year_min = date('Y' , strtotime($result->getDateDepart()->format('Y-m-d')));
           /**
            * @var $newerDepart[0] Departs
            */
            $newerDepart = $this->findBy(['agence' => $agence] , ['dateDepart' => 'DESC'] , ['limit' => 1]);
            $year_max = date('Y' , strtotime($newerDepart[0]->getDateDepart()->format('Y-m-d')));

            $year_number = ($year_max-$year_min)+1;

        } else {
            $year_number = 1;
            $year_min = date('Y');
        }

        $conn = $this->getEntityManager()->getConnection();

        for($i = 0 ; $i < $year_number ; $i++) {
            $current_year = $year_min+$i;

            for ($j = 1 ; $j < 13 ; $j++) {

                $requete = 'SELECT date_depart  FROM departs WHERE agence_id = '.$id_agence.' 
                AND MONTH(date_depart) = '.$j.' AND YEAR(date_depart) = '.$current_year.' limit 1';

                $stmt = $conn->query($requete);
                $stmt->execute();
                if($stmt->rowCount() !=0) {
                    $filter[] = self::$mois[($j-1)]."-".$current_year;
                }
            }
        }
        return $filter;
    }

    public function getCustomDepart(Agences $agence , string $dateMin , string $dateMax) {

        return $this->createQueryBuilder('d')
            ->andWhere("d.agence = ?0")
            ->andWhere("d.dateDepart >= ?1")
            ->andWhere('d.dateDepart <= ?2')
            ->setParameters(['0'=>$agence , '1'=>$dateMin , '2'=>$dateMax])
            ->orderBy('d.id', 'DESC')
            ->getQuery()->getResult();

    }


    public function getDepartForComparator(string $dateMin , Location $origine , Location $destination , $price) {
        $dateMax = new \DateTime($dateMin);
        $dateMax->modify('+ 1 days');
        dump($dateMax);
        return $this->createQueryBuilder('d')
            ->andWhere("d.dateDepart >= ?1")
            ->andWhere("d.dateDepart <= ?2")
            ->andWhere("d.origine = ?3")
            ->andWhere("d.destination = ?4")
            ->andWhere("d.tarifAdult <= ?5")
            ->setParameters(['1'=>$dateMin , '2'=>$dateMax , '3'=>$origine , '4'=>$destination , '5'=>$price])
            ->orderBy('d.id', 'DESC')
            ->getQuery()->getResult();
    }


//    /**
//     * @return Departs[] Returns an array of Departs objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Departs
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
