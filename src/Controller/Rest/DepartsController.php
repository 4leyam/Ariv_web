<?php
/**
 * Created by PhpStorm.
 * User: ryans
 * Date: 08-11-2018
 * Time: 10:09
 */

namespace App\Controller\Rest;


use App\Entity\Departs;
use App\Entity\Location;
use App\Repository\DepartsRepository;
use App\Repository\LocalisationRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;

class DepartsController extends Controller implements ClassResourceInterface
{


    /**
     * @var $departRepository DepartsRepository
     */
    private $departRepository = null;
    private $data = null;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->departRepository = $doctrine->getRepository(Departs::class);

    }

    /**
     * @Rest\View()
     * methode permettant de recuperer un depart en particulier.
     */
    public function getAction($slug) {
        if(is_numeric($slug)) {

            $this->data = $this->departRepository->find($slug);
            $agence = $this->data->getAgence();

            $agence->setDeparts(null);
            $agence->setNzelaUsers(null);
            $agence->setInvitationTokens(null);
            $agence->setComments(null);

            return $this->data;

        } else {
            return new Exception("the slug should be a number");
        }

    }


    /**
     * @Rest\View()
     * methode permettant de recuperer un depart en function de certains criteres.
     */
    public function postFilterAction(Request $request) {
        //les information de localite recuent dans la requete doivent etre les noms des villes.


        /**
         * @var $locationRep LocalisationRepository
         */
        $locationRep = $this->getDoctrine()->getRepository(Location::class);
        $origine = $locationRep->findBy(['ville'=>$request->get('origine')]);
        $destination = $locationRep->findBy(['ville'=>$request->get('destination')]);

        $max = $request->get('max');
        $date = $request->get('date');
        if(isset($date , $max , $origine ,$destination) and (sizeof($origine ) > 0 and sizeof($destination) > 0)) {
            /**
             * @var $tmp Departs[]
             */
            $tmp = $this->departRepository->getDepartForComparator($date , $origine[0] , $destination[0] , $max);
            $departs = null;
            foreach ($tmp as $dep) {
                $agence = $dep->getAgence();
                $agence->setDeparts(null);
                $agence->setComments(null);
                $agence->setNzelaUsers(null);
                $agence->setInvitationTokens(null);
                $dep->setAgence($agence);
                $departs[] = $dep;
            }
            return $departs;
        } else {
            return ['status'=>500 , 'Exception'=>"Payload invalide"];
        }
    }



}







