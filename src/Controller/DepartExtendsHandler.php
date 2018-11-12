<?php
/**
 * Created by PhpStorm.
 * User: babygracias
 * Date: 9/29/2018
 * Time: 1:01 AM
 */

namespace App\Controller;


use App\Entity\Departs;
use Symfony\Bridge\Doctrine\ManagerRegistry;

class DepartExtendsHandler {

    public function extendsOneWeek(Departs $depart , ManagerRegistry $doctrine) :string {


        $entityManager = $doctrine->getManager();
        $date = $depart->getDateDepart();
        $lastAddedId = array();


        /*on commence par 1 parceque si on commence par zero on aura deux l'agence qui extends sera double
         *a zero la date ne sera pas modifiee
         */
        for ($i = 1 ; $i<8 ; $i++) {

            $tmp = new \DateTime($date->format('Y-m-d H:i:s'));
            $departTmp = new Departs();
            $departTmp->setPlaceRestante($depart->getPlaceInit());
            $departTmp->setFormalite($depart->getFormalite());
            $departTmp->setPlaceInit($depart->getPlaceInit());
            $origine = $depart->getOrigine($doctrine , true);
            $departTmp->setOrigine($origine);
            $destination = $depart->getDestination($doctrine , true);
            $departTmp->setDestination($destination);
            $departTmp->setImageBus($depart->getImageBus());
            $departTmp->setTarifEnfant($depart->getTarifEnfant());
            $departTmp->setTarifAdult($depart->getTarifAdult());
            $departTmp->setDepart($depart->getDepart());
            $departTmp->setDateDepart($tmp->modify("+".$i." day"));
            $departTmp->setFormalite($depart->getFormalite());
            $departTmp->setValide(true);
            $departTmp->setAgence($depart->getAgence());
            $entityManager->persist($departTmp);
            $entityManager->flush();
            $lastAddedId[] = [ "id"=> $departTmp->getId() , "places"=>$departTmp->getPlaceInit()];
        }

        return SubmitHandler::getInstance()->updateFirebaseDep($lastAddedId);

    }

}