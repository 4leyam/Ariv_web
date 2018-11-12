<?php
/**
 * Created by PhpStorm.
 * User: babygracias
 * Date: 9/14/2018
 * Time: 8:02 PM
 */

namespace App\Controller;


use App\Entity\Agences;
use App\Entity\Departs;
use App\Form\FormAlertDialog;
use App\Repository\DepartsRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Self_;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AdminHistorique implements AlertDialogInterface{

    public const EXTEND_MODE = 0;
    public const SELF_MODE = 1;
    private $currentMode = 0;
    private static $instance = null;
    /**
     * @var $formFactory FormFactory
     */
    private $formFactory = null;
    /**
     * @var $formAlertDialog FormInterface
     */
    private $formAlertDialog = null;
    /**
     * @var $historyDefinition MenuContentDefinition
     */
    private $historyDefinition = null;
    /**
     * @var $doctrine AbstractController
     */
    private $alertStringContent = null;
    private $doctrine = null;
    private $currentAgence = null;
    /**
     * @var $session SessionInterface
     */
    private $session = null;
    private $request = null;
    private $js = null;


    public static function getDepartHystoryFactory(ManagerRegistry $doctrine , int $mode = null): AdminHistorique {
        if(is_null(self::$instance)) {
            switch ($mode) {
                case self::SELF_MODE:
                    self::$instance = new AdminHistorique($doctrine , self::SELF_MODE);
                    break;
                default:
                    self::$instance = new AdminHistorique($doctrine );
                    break;
            }
        }
        return self::$instance;
    }

    private function __construct(ManagerRegistry $doctrine = null , ?int $mode = null ) {
        $this->currentMode = is_null($mode)?0:$mode;
        $this->doctrine = $doctrine;
    }

    public function createHistoryHeaderForm(int $tab , SessionInterface $session , Request $request = null) {
        $this->session = $session;
        $this->request = $request;
        $this->handleRalonge($this->request);
        $this->historyDefinition = new MenuContentDefinition($tab , true , [
            AbstractMenuContent::EVENT_TYPPE => is_null($this->currentAgence)
                ? FormEvents::POST_SUBMIT
                : FormEvents::PRE_SET_DATA,
            AbstractMenuContent::EVENT_CALLBACK => $this->departsAgenceListEventListener()
        ] , false);
        $this->setHistoryData(null);
        return $this->historyDefinition;

    }

    /**
     * cette methode definit la methode de callback a appeler apres lors de l'evenement
     * associe a la liste.
     */
    private function departsAgenceListEventListener() {

        return function (FormEvent $formEvent) {
            $this->currentAgence = $formEvent->getData();
            $parent = $formEvent->getForm()->getParent();
            /**
             * @var $selectedAgence Agences
             */
            $selectedAgence = $this->currentAgence;
            if(!is_null($selectedAgence)) {
                $selectedAgence = $this->doctrine->getRepository(Agences::class)->find($selectedAgence);
                $periodes = $this->getDepartPeriods($this->doctrine->getRepository(Departs::class) , $selectedAgence);
                $this->currentAgence = $selectedAgence;

                $this->currentTab = $parent->get('tab')->getData();
                $buider = $parent->getConfig()->getFormFactory()->createNamedBuilder(
                    'Periodes' ,
                    ChoiceType::class ,
                    null ,
                    [
                        'choices' =>$periodes,
                        'choice_label' => function ($choiceValue, $key, $value) {
                            return $value;
                        },
                        'mapped'=>false,
                        'placeholder' => 'Selectionnez une periode',
                        'auto_initialize'=>false,
                        'attr'=>[
                            'class'=>"selectpicker show-menu-arrow",
                            'label'=>false,
                            'data-live-search'=>"true",
                            'onchange'=>'submit()'
                        ]
                    ]);
                $buider->addEventListener(FormEvents::POST_SUBMIT , function (FormEvent $event) {
                    $this->departPeriodeEventListener($event);
                });
                $parent->add($buider->getForm());
            } else {
                $this->setHistoryData(null);
            }
        };

    }


    private function departPeriodeEventListener(FormEvent $events) {
        //on recupere la periode qui nous interesse.
        $data = $events->getData();
        if($data) {
            $dr = $this->doctrine->getRepository(Departs::class);
            $handler = DepartHandler::getGlobalInstance();
            $rawDeparts = $handler->getDepartByPeriod($this->currentAgence , $data , $dr , $this->session);
            $this->setHistoryData($rawDeparts);

        }


    }

    private function setHistoryData($rawDeparts) {
        $tmp = is_null($rawDeparts)
            ?['' , '']
            :$rawDeparts;

        /**
         * on ajoute l'index de la table aux donnees a envoyer pour cette clee
         * pour que l'onglet soit automatiquement indexee la cle de la donnee de l'index doit etre
         * parent::TAB_KEY
         * pareil pour les donnees. en gros les donnes sont placees dans un table avec pour cle TABKEY , et TABCONTENT
         * si c'est pas claire copy et colle les cles partout ou tu dois definir des donnees d'onglets.
         */
        $historyData = [];
        $historyData[AbstractMenuContent::TAB_KEY ] = $this->historyDefinition->getTab();
        $historyData[AbstractMenuContent::TAB_CONTENT] = array(
            "departs" => $tmp[0],
            "titles"=>$tmp[1],
            "ralongeListForm"=>($tmp[0] == '')
                                            ? []
                                            : $this->initRalongeForm($tmp[1]),
            'alertDialogContent'=>$this->alertStringContent
        );
        $this->historyDefinition->setData($historyData);
        $this->historyDefinition->setExtraGlobalData($this->js);
    }

    public function setAlertForm()
    {
        // TODO: Implement setAlertForm() method.
        $formDialog = new FormAlertDialog(
            'En Confirmant vous acceptez de reproduire les
             informations de ce depart sur
              la duree de l\'interval selectionneé' ,
            'Confirmation' , DepartAdminController::HISTORY);
        $this->setAlertStringContent($formDialog->getDialogView());

    }


    public function setCurrentAgence(?Agences $agence) {
        $this->currentAgence = $agence;
    }

    private function handleRalonge(Request $request) {
        /*
         *comment les information du formulaire de ralonge des departs est gere.e
         * depend de comment les donnees sont soumit a travers une boite de dialog ou directement.
         *
         * actu les donnees sont soumis via une boite de dialog
         */
        $keys = $request->request->keys();
        if(sizeof($keys) > 0) {
            $tmp = $request->request->get($keys[0]);

            //pour commencer il faut persister l'onglet dans lequel se deroule traitement.
            if (is_array($tmp) && sizeof($tmp) > 0) {
                //instruction qui sera executer que si un formulaire dans l'onglet courant est soumis
                if(array_key_exists("departId" , $tmp)) {
                    $departId = $tmp["departId"];
                    $extender = new DepartExtendsHandler();
                    $this->js = $extender->extendsOneWeek($this->doctrine->getRepository(Departs::class)->find($departId) , $this->doctrine);
                    $this->session->set(DepartAdminController::DEPART_ADMIN_TAB_KEY , DepartAdminController::HISTORY);
                }
            }
        }
    }


    /**
     * methode permettant de definir pour chaque depart un possibilite (un formulaire) de ralonge
     *
     * @param array $titles
     * @return array
     */
    private function initRalongeForm(array $titles) :array {

        $j = 0;
        $ralonges = [];
        dump("pas de maladresse");

        foreach ($titles as $title=>$content) {
            $name = $j;

            if(sizeof($content) > 0) {
                foreach ($content as $depart) {
                    $tmpForm = $this->formFactory->createNamed($name , FormAlertDialog::class , new UnmappedForm());
                    $ralonges[] = $tmpForm->createView();
//                    if(!is_null($this->request)) {
//                        $tmpForm->handleRequest($this->request);
//                        if($tmpForm->isSubmitted()) {
//                            dump("pas de maladresse");
//                        }
//                    }
                    $j++;
                    $name = $j;
                }
            }

        }
        dump($ralonges);
        return $ralonges;
    }

    public function setFormFactory(FormFactory $formFactory) {
        $this->formFactory = $formFactory;
    }

    public function setAlertStringContent(string $alertStringContent) {
        // TODO: Implement setAlertStringContent() method.
        $this->alertStringContent = $alertStringContent;
    }

    /**
     * methode permettant de regrouper les departs d'une agence en groupe de periode ( mois-Annee )
     *
     * @param DepartsRepository $departsRepository
     * @param Agences|null $agence
     * @return array
     */

    private function getDepartPeriods(DepartsRepository $departsRepository , ?Agences $agence) {
        if(!is_null($agence))
        return $departsRepository->getDepartPeriode($agence );
    }


}