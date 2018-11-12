<?php

namespace App\Controller;

use App\Entity\Departs;
use App\Entity\Location;
use App\Repository\DepartsRepository;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ComparateurController
{
    /**
     * @param AbstractController $controller
     * @param Request $request
     * @param SessionInterface $session
     * @return mixed
     */

    /**
     * @var $doctrine ManagerRegistry
     */
    private static $doctrine = null;
    private static $request = null;
    private static $session = null;
    private $data = [];
    private static $instance = null;


    /**
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param bool $reload
     * @param SessionInterface|null $session
     * @return ComparateurController
     */
    public static function getInstance(ManagerRegistry $doctrine ,FormInterface $formComparateur , Request $request , bool $reload = false , SessionInterface $session = null) : ComparateurController
    {
        self::$doctrine= $doctrine;
        self::$request = $request;
        self::$session = $session;

        self::$instance = $reload
            ?new ComparateurController($request , $formComparateur)
            :self::$instance;
        return self::$instance;
    }

    private function __construct(Request $request , FormInterface $formComparateur)
    {
        $wantedDeparts = ["" , ""];
        $formView = $formComparateur;
        if($formView->isSubmitted() and $formView->isValid()) {
            /**
             * @var $origine Location
             * @var $destination Location
             */
            $origine = $formView->get('origine')->getData();
            $destination = $formView->get('destination')->getData();
            dump($formComparateur);
            $date = $formView->get('date')->getData();
            $maxPrice = $formView->get('prix')->getData();
            $wantedDeparts = $this->getWantedDepart($origine , $destination , $date , $maxPrice);
            $wantedDeparts = DepartHandler::getGlobalInstance()
                ->organizeDep(
                    $wantedDeparts ,
                    self::$doctrine->getRepository(Departs::class)
                );
            dump($wantedDeparts);
        }

        $this->setData([
            'page' => "comparateur" ,
            'formComparateur'=> $formView->createView(),
            'submitted'=>$formView->isSubmitted(),
            'dateTitle' => $wantedDeparts[1] ,
            "departs" => $wantedDeparts[0]
        ]);

    }



    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    private function getWantedDepart(Location $origine , Location $destination , $date , $price) : array {

        return self::$doctrine
            ->getRepository(Departs::class)
            ->getDepartForComparator(
                $date->format('y-m-d') ,
                $origine ,
                $destination , $price);
    }
}
