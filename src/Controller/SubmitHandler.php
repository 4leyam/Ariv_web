<?php
/**
 * Created by PhpStorm.
 * User: babygracias
 * Date: 9/16/2018
 * Time: 10:50 PM
 */

namespace App\Controller;

use App\Entity\Agences;
use App\Entity\Departs;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class SubmitHandler {


    private static $instance = null;

    public function __construct() {

    }

    public static function getInstance() : SubmitHandler {
            if(is_null(self::$instance)) {
                self::$instance = new SubmitHandler();
            }

            return self::$instance;

    }


    /**
     *cette methde permet de traiter tous les formulaire de DepartAdmin
     *@param $departDirectory
     *@param $depart
     *@param $doctrine
     *@param $operation
     * @return string
     */
    public function handlerDepartAdminForm(int $operation , Departs $depart , ?string $departDirectory , ManagerRegistry $doctrine) {
        //departDirectory vaut null qand y'a pas de modification sur l'ancienne image.
        $fileName = AppConstants::DEPART_DEFAULT_FILE_NAME;
        $imageBus = $depart->getImageBus();
        if(!is_null($departDirectory) and $imageBus instanceof UploadedFile) {
            $fileName = $this->moveFile($depart , $departDirectory);
        } else if(!is_null($imageBus)) {
            $fileName = $imageBus;
        }

        $depart->setImageBus($fileName);
//        dump($depart);
        $entityManager = $doctrine->getmanager();

        //on redefinit les origines et les destination en objet pour permettre la suppression
        $origine = $depart->getOrigine($doctrine , true);
        $destination = $depart->getDestination($doctrine , true);
        $depart->setOrigine($origine);
        $depart->setDestination($destination);
        switch ($operation) {
            case DepartAdminController::ADD:
                $depart->setPlaceRestante($depart->getPlaceInit());
                $entityManager->persist($depart);
                break;

            case DepartAdminController::SET:
                $settedDep = $entityManager->getRepository(Departs::class)->find($depart->getId());
                $settedDep->setPlaceRestante($depart->getPlaceRestante());
                $settedDep->setFormalite($depart->getFormalite());
                $settedDep->setPlaceInit($depart->getPlaceInit());
                $settedDep->setOrigine($origine);
                $settedDep->setDestination($destination);
                $settedDep->setImageBus($depart->getImageBus());
                $settedDep->setTarifEnfant($depart->getTarifEnfant());
                $settedDep->setTarifAdult($depart->getTarifAdult());
                $settedDep->setDepart($depart->getDepart());
                $settedDep->setDateDepart($depart->getDateDepart());
                $settedDep->setFormalite($depart->getFormalite());
                $entityManager->persist($settedDep);
                break;
            case DepartAdminController::DELETE:
//                dump($depart);

                $depart = $entityManager->merge($depart);
                $entityManager->remove($depart);
                break;
        }
        $entityManager->flush();
        $newDep = [];
        $newDep[] = ["id"=>$depart->getId() , "places"=>$depart->getPlaceInit()];
        return $this->updateFirebaseDep($newDep);
    }

    private function moveFile($entity , string $directory) :string {
        //departDirectory depart $this->getParameter('depart_directory'),
        //l'obet depart ici nous permet de recuperer l'emplacemen provisoir de l'image.
        /**
        *@var $file UploadedFile
        * */
        $file = null;
        if($entity instanceof Departs) {

            $file = $entity->getImageBus();

        } else if ($entity instanceof Agences) {

            $file = $entity->getAgenceLogo();
        }
        $fileName = $this->generateUniqFileName().".".$file->guessExtension();
        //ensuite on deplace lefichier dans le repertoire des Images des bus
        $file->move(
            $directory ,
            $fileName
        );

        return $fileName;

    }

    /**
     * cette methode traite les formulaire d'administration des agences.
     *
     * @param int $operation
     * @param Agences $agence
     * @param null|string $agenceDirectory
     * @param ManagerRegistry $doctrine
     */
    public function handleAgenceAdminForm(int $operation , Agences $agence , ?string $agenceDirectory , ManagerRegistry $doctrine) {
        //departDirectory vaut null qand y'a pas de modification sur l'ancienne image.
        $fileName = AppConstants::AGENCE_DEFAULT_FILE_NAME;
        $incomingLogo = $agence->getAgenceLogo();
        if(!is_null($agenceDirectory) and  $incomingLogo instanceof UploadedFile) {
            $fileName = $this->moveFile($agence , $agenceDirectory);
        } else if(!is_null($incomingLogo)) {
            $fileName = $incomingLogo;
        }
        $agence->setAgenceLogo($fileName);
        $entityManager = $doctrine->getmanager();
        switch ($operation) {
            case AgenceAdminController::ADD:
                $agence->setAvis(0);
                $entityManager->persist($agence);
                break;

            case DepartAdminController::SET:
                /**
                 * @var $settedAgence Agences
                 */
                $settedAgence = $entityManager->getRepository(Agences::class)->find($agence->getId());
                $settedAgence->setAvis($agence->getAvis());
                $settedAgence->setAgenceLogo($agence->getAgenceLogo());
                $settedAgence->setEmailAgence($agence->getEmailAgence());
                $settedAgence->setPlusInfo($agence->getPlusInfo());
                $settedAgence->setContactAgence($agence->getContactAgence());
                $settedAgence->setAdresseAgence($agence->getAdresseAgence());
                $settedAgence->setNomAgence($agence->getNomAgence());
                break;
            case DepartAdminController::DELETE:
//                dump($agence);
                $agence = $entityManager->merge($agence);
                $entityManager->remove($agence);
                break;
        }
        $entityManager->flush();
        $agence->getId();

    }

    private function generateUniqFileName() {
         //on combine uniqid et md5 pour avoir un nom de fichier persque unique.
        return md5(uniqid());
    }

    /**
     * cette methode permet de mettre a jours les departs sur firebase quand des depart est ajoute. .
     * @param array $depInfo
     * @return string
     */
    public function updateFirebaseDep(array $depInfo):string {

            $js = '<script type="text/javascript">';
            $functionCall = "";
            foreach ($depInfo as $info) {
                $functionCall.= 'newDepart('.$info["id"].' , '.$info["places"].');';
            }
            $js.='$(document).ready(function () {'.$functionCall.'}); </script>';
            return $js;
       ;
    }
}