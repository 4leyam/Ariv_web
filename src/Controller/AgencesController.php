<?php

namespace App\Controller;

use App\Entity\Agences;
use App\Form\FormComparateur;
use App\Repository\AgencesRepository;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class AgencesController extends AbstractController
{
    /**
     * @Route("/agences", name="agences")
     */
    public function index(SessionInterface $session , Request $request)    {

        $form = $this->createFilterForm($session);
        $form->handleRequest($request);
        $index = $session->get("selected" , 0);

        //comme son nom l'indique on initialise le comparateur.

        $comparateurData = $this->initComparateur($this->getDoctrine() , $request , $session);



        return $this->render('agences/agenceList.html.twig', [
            'page' => 'agences',
            'agences' => $this->getFilteredAgences($index),
            'classementDescription' => $this->getClassementDescription($index),
            'form' => $form->createView(),
            'searchForm'=>$this->addAgenceSearchBar(),
            'comparateur'=>$comparateurData
        ]);
    }

    /**
     * recupere les agences selon l'indice de filtre.
     *
     * @param int $filter
     * @return null
     */
    private function getFilteredAgences(int $filter) {
        /**
         * @var $repository AgencesRepository
         */
        $repository = $this->getDoctrine()->getRepository(Agences::class);
        //$agences est la liste des agences filtree.
        $agences = null;
        switch ($filter) {
            case 0:
                $agences = $repository ->findTop();
                break;
            case 1:
                $agences = $repository ->findRecents();
                break;
            case 2:
                $agences = $repository ->findOlds();
                break;
        }
        return $agences;
    }

    /**
     * reture la description de l'ensemble des agence affiches
     *
     * @param int $filter
     * @return string
     */
    public function getClassementDescription(int $filter) {
        switch ($filter) {
            case 0:
                return "Liste des agences les plus cotees";
            case 1:
                return "Liste des agences les plus Recentes";
            case 2:
                return  "Liste des agences les plus Anciennes";
        }
    }

    /**
     * creer le formulaire pour faire des recherches
     * sur les agence et return la vue du formulaire.
     *
     * @return \Symfony\Component\Form\FormView
     */
    public function addAgenceSearchBar() {
        $unmaped = new UnmappedForm();
        $searchForm = $this->createFormBuilder($unmaped)->add('search' ,TextType::class , ['mapped'=>false , "label"=>false]);

        $searchForm = $searchForm->getForm();
        return $searchForm->createView();
    }

    public function createFilterForm(SessionInterface $session) {
        $unmaped = new UnmappedForm();
        $builder = $this->createFormBuilder($unmaped)->
        add('classement' , ChoiceType::class
            , [
                "choices" =>[
                    "les plus cotes" =>0,
                    "les Plus recentes" =>1,
                    "les plus Anciennes" => 2
                ],
                "label"=>false,
                "attr"=>['class' =>'form-control' , 'onchange'=>'submit()'],
                "data"=>$session->get('selected'),
                "mapped"=>false
            ]);
            $builder->get('classement')->addEventListener(FormEvents::POST_SUBMIT , function (FormEvent $formEvent) use ($session) {
            $form = $formEvent->getForm();
            $index = $form->getData();
            $session->set("selected" , $index);
            $formEvent->setData(["pas de maladresse" => 0]);

        });
        return $builder->getForm();
    }


    private function initComparateur(ManagerRegistry $doctrine , Request $request , SessionInterface $session) {
        $builder = $this->createForm(FormComparateur::class , new UnmappedForm());
        $builder->handleRequest($request);

        $comparateur = ComparateurController::getInstance($doctrine , $builder , $request , true , $session);
        return $comparateur->getData();
    }

}
