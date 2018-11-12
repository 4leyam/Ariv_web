<?php

namespace App\Controller;

use App\Entity\Agences;
use App\Entity\Departs;
use App\Entity\NzelaUser;
use App\Form\FormAlertDialog;
use App\Form\FormDepartAdminType;
use App\Repository\AgencesRepository;
use App\Repository\DepartsRepository;
use Doctrine\ORM\EntityRepository;
use phpDocumentor\Reflection\Types\Callable_;
use phpDocumentor\Reflection\Types\Parent_;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;

class DepartAdminController extends AbstractMenuContent {



    public const ADD = 0;
    public const SET = 1;
    public const DELETE = 2;
    public const EXTEND = 4;
    public const HISTORY = 3;
    public const SESS_AGENCE_KEY = "SAGK";
    public const SESS_DEPART_KEY = "SdpK";
    public const DEPART_ADMIN_TAB_KEY = "DATK";
    public const LAST_SELECTED_AGENCE = "lsa";
    public const LAST_DEPART_BUS_IMAGE_KEY = "ldbik";

    /**
     * @var $currentDepart Departs
     */
    private $currentDepart = null;
    protected $currentAgence = null;
    protected $currentTab = 0;
    private $request = null;
    protected $isAdminAgence = false;
    /**
     * @var $sessionInterface SessionInterface
     */
    private $sessionInterface = null;


    /**
     * @Route("admin/depart", name="depart_admin")
     *
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request , SessionInterface $session) {
        $this->request = $request;
        $this->sessionInterface = $session;


        $menuContentDefinition = $this->initHystory();
        parent::synchUserNAgence();
        $this->initPageState($this->request);




        /*
         * on cree un tableau contenant les differents onglets et leurs donnee
         * les donnees et les definition des pseudo comportement sont contenu dans l'objet
         * MenuContentDefinition.
         */
        $onglets = [
            'Ajouter'=>new MenuContentDefinition(self::ADD , false),
            'Modifier'=>new MenuContentDefinition(self::SET , true , [
                parent::EVENT_TYPPE => FormEvents::POST_SUBMIT,
                parent::EVENT_CALLBACK => $this->departsAgenceListEventListener()
            ]),
            'Supprimer'=>new MenuContentDefinition(self::DELETE , true , [
                parent::EVENT_TYPPE => FormEvents::POST_SUBMIT,
                parent::EVENT_CALLBACK => $this->departsAgenceListEventListener()
            ]),
            'Historique & Extention'=>$menuContentDefinition,

        ];

        if($this->isGranted(AppConstants::ROLES[3])) {
            $allow = true;
        } else if($this->isGranted(AppConstants::ROLES[1])) {

            /**
             * @var $user NzelaUser
             */
            $user = $this->getUser();
            $allow = is_null($this->currentAgence)
                ? false
                : AppConstants::getObjectAuthorization($user , $this->currentAgence);
        } else $allow = false;

        dump($this->js);
        dump($menuContentDefinition->getExtraGlobalData().$this->js);

        return parent::renderAllContent($onglets ,
            $this->currentTab ,
            'depart_admin/departAdmin.html.twig' ,
            $allow ,
            [AbstractMenuContent::EXTRA_JS_KEY=>$menuContentDefinition->getExtraGlobalData()]);
    }


    public function createMainForm(int $tabIndex): FormInterface {
        /*
         * cette methode peut etre definit comme bon vous semble tout depend
         * de comment vous proceder pour recuperer les donnees du contenu de votre onglet
         */
        switch($tabIndex) {
            case self::EXTEND:
                //on prevoit le traitement pour les clonage.

            default:
                $form =  $this->createDepartAdminForm($tabIndex);
                return $form;
        }

    }

    public function createDepartSideForm(int $tab , array $eventData) {

        if(!is_null($this->currentAgence) && !($this->currentAgence instanceof Agences)) {
           $this->currentAgence = $this->getDoctrine()->getRepository(Agences::class)->find($this->currentAgence);
        }

        $builder = $this->createFormBuilder(new UnmappedForm())
            ->add('tab' , NumberType::class , [
                'label'=>false,
                'mapped'=>false,
                'data'=>$tab,
                'attr'=>[
                    'hidden'=>'hidden',
                    'value'=>$tab
                ]
            ])
            ->add("agences" , EntityType::class , [
                'class'=>Agences::class,
                'choice_label'=>"nomAgence",
                'mapped'=>false,
                'data'=>$this->currentAgence ,
                'placeholder' => 'Selectionnez une agence',
                'query_builder' => function (EntityRepository $er) {
                    /**
                     * @var $er AgencesRepository
                     */
                    return $er->getFindRecentBuilder($this->isAdminAgence , $this->getUser());
                },

                'attr'=>[
                    'class'=>"selectpicker show-menu-arrow",
                    'data-style'=>"btn-warning",
                    'data-live-search'=>"true",
                    'onchange'=>'submit()',
                ]
            ]);

        $builder->get('agences')->addEventListener($eventData[parent::EVENT_TYPPE] , function (FormEvent $formEvent) use ($eventData){
            $eventData[parent::EVENT_CALLBACK]($formEvent);
        });
        $form = $builder->getForm();
        $form->handleRequest($this->request);
        return $form;
    }

    public function initPageState(Request $request) {
        // TODO: Implement initPageState() method.
        $this->currentTab = $this->sessionInterface->get(self::DEPART_ADMIN_TAB_KEY , self::ADD );
        $this->currentAgence = $this->sessionInterface->get(self::LAST_SELECTED_AGENCE , null);
        if(!is_null($request)) {
            $form = $request->request->get('form');
            if(!is_null($form)) {
                if(array_key_exists('tab' , $form)) {
                    $this->currentTab = $form['tab'];
                }
                if(array_key_exists('agences' , $form)) {
                    $this->sessionInterface->set(self::LAST_SELECTED_AGENCE ,$form['agences']);
                }

            }

        }
        $this->currentAgence = $this->sessionInterface->get(self::LAST_SELECTED_AGENCE , null);
    }

    public function createSecondForm(int $tabIndex, array $eventData): FormInterface {
        // TODO: Implement createSecondForm() method.
        return $this->createDepartSideForm($tabIndex , $eventData);
    }

    /**
     * cette methode definit la methode de callback a appeler apres lors de l'evenement
     * associe a la liste
     */
    private function departsAgenceListEventListener() : callable {
        return function (FormEvent $formEvent) {
            $this->currentAgence = $formEvent->getData();
            $parent = $formEvent->getForm()->getParent();
            $this->sessionInterface->set(self::LAST_SELECTED_AGENCE , $this->currentAgence);
            /**
             * @var $selectedAgence Agences
             */
            dump($this->currentAgence);
            if($this->currentAgence) {
                $selectedAgence = $this->currentAgence;
                $selectedAgence = $this->getDoctrine()->getRepository(Agences::class)->find($selectedAgence);
                $this->currentAgence = $selectedAgence;
                if(!is_null($selectedAgence)) {

                    $this->currentTab = $parent->get('tab')->getData();
                    $buider = $parent->getConfig()->getFormFactory()->createNamedBuilder(
                        'departs' ,
                        EntityType::class ,
                        $this->currentAgence->getDeparts()->get(0) ,
                        [
                            'class'=>Departs::class,
                            'choice_label'=>function(Departs $depart =null){
                                return ($depart != null)
                                    ?$depart->getOrigine()."-".$depart->getDestination().'-|-'.$depart->getDateDepart()->format('d-m-Y')
                                    :"";
                            }
                            ,
                            'placeholder' => 'Selectionnez un depart',
                            'mapped'=>false,
                            'auto_initialize'=>false,
                            'query_builder' => function (EntityRepository $er) use($selectedAgence) {
                                /**
                                 * @var $er DepartsRepository
                                 */
                                return $er->getFindRecentBuilder($selectedAgence);
                            },
                            'attr'=>[
                                'class'=>"selectpicker show-menu-arrow",
                                'data-style'=>"btn-warning",
                                'label'=>false,
                                'data-live-search'=>"true",
                                'onchange'=>'submit()'
                            ]
                        ]);
                    $buider->addEventListener(FormEvents::POST_SUBMIT , function (FormEvent $event) {
                        $this->departEventListener($event);
                    });
                    $parent->add($buider->getForm());
                }
            }

        };
    }

    private function departEventListener(FormEvent $event) {
        $depart = $event->getData();
        $depart = $this->getDoctrine()->getRepository(Departs::class)->find($depart);
        if(!is_null($depart)) {
            $this->currentDepart = $depart;

            $parent = $event->getForm()->getParent();
            if(!is_null($parent)) {
                $ancestor = $parent->getParent();
                if(!is_null($ancestor)) {
                    $this->currentTab = $ancestor->get('tab')->getData();
                } else {
                    $this->currentTab = $parent->get('tab')->getData();
                }
                $this->currentDepart->tab = $this->currentTab;
            }
        }


    }

    private function createDepartAdminForm(int $formType ) {
        $block_name = array('add' , 'set' , 'del');
        $currentDepart = $this->currentDepart;
        $depart = is_null($currentDepart)
            ?new Departs()
            :$currentDepart;
        /**
         * @var $formFactory FormFactory
         */
        $formFactory = $this->get('form.factory');
        $form = $formFactory->createNamed(
            'departAdmin'.$formType ,
            FormDepartAdminType::class ,
            $depart ,
            [
                'block_name'=>$block_name[$formType] ,
                'user'=>$this->getUser() ,
                'isAgenceAdmin'=>$this->isAdminAgence
            ] );
        $form->handleRequest($this->request);
        if(!$form->isSubmitted()) {
            if($formType != self::ADD) {
                $this->sessionInterface->set(self::LAST_DEPART_BUS_IMAGE_KEY , $depart->getImageBus());
            }
            if(!is_null($this->currentDepart)) {
                $form->get("id")->setData($this->currentDepart->getId());
            }
            $form->add('tab' , NumberType::class , ['mapped'=>false,
                'label'=>false,
                'data'=>$formType,
                'attr'=>[
                    'hidden'=>'hidden',
                ]
            ]);
        } else {
            if($form->isValid()) {
                $this->currentTab = $formType;
                $departDirecetory = $this->getParameter('depart_directory');
                if(is_null($depart->getImageBus())) {
                    $depart->setImageBus($this->sessionInterface->get(self::LAST_DEPART_BUS_IMAGE_KEY ,
                        AppConstants::DEPART_DEFAULT_FILE_NAME));
                }
                /**
                 * @var $tmp UploadedFile
                 */
                $tmp = $depart->getImageBus();
                if(is_null($tmp)) {
                    $departDirecetory = null;
                }
                $handler = SubmitHandler::getInstance();

                switch ($formType) {
                    case self::ADD:
                        $depart->setValide(1);
                        $this->js = $handler->handlerDepartAdminForm($formType , $depart , $departDirecetory , $this->getDoctrine());
                        break;
                    default:

                        $currentDepart = $depart;
                        $this->currentDepart = $currentDepart;
                        //pas de set Id alors on fait les choses differement.
                        if(!is_null($currentDepart) and !is_null($this->currentAgence)) {
                            dump('debut de la modification');
                            //les deux set sont la precisement pour le cas de la suppression d'un depart.
                            $currentDepart->setValide(1);
                            $currentDepart->setAgence($this->getDoctrine()->getRepository(Agences::class)->find($this->currentAgence));
                            $handler->handlerDepartAdminForm($formType , $currentDepart , $departDirecetory , $this->getDoctrine());
                        }
                        break;
                }
                $this->extraInfo = ['message'=>'Operation effectuee avec succes :)' , 'type'=>'success'];

            }
        }

        return $form;

    }

    private function initHystory() :MenuContentDefinition {
        //je suis oblige d'appeler initPageState pourameliorer le rendu de l'onglet historique fait en sorte que l'agence et la periode soit completement selectionne.e
        $this->initPageState($this->request);
        $history = AdminHistorique::getDepartHystoryFactory($this->getDoctrine());
       /**
        * @var $formFactory FormFactory
        */
        $formFactory = $this->get('form.factory');
        $history->setAlertForm();
        $history->setFormFactory($formFactory);
        $history->setCurrentAgence(($this->currentAgence instanceof Agences)
            ? $this->currentAgence
            :!is_null($this->currentAgence)
                ?$this->getDoctrine()->getRepository(Agences::class)->find($this->currentAgence)
                :null
        );
        return $history->createHistoryHeaderForm(self::HISTORY , $this->sessionInterface , $this->request);
   }



}


