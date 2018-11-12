<?php
 namespace App\Controller;

 use App\Entity\Agences;
 use App\Entity\Departs;
 use App\Entity\NzelaUser;
 use App\Form\FormAccesProvider;
 use App\Form\FormAgenceAdminType;
 use App\Form\FormInviteAdmin;
 use App\Repository\AgencesRepository;
 use App\Repository\DepartsRepository;
 use Doctrine\ORM\EntityRepository;
 use Monolog\Logger;
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


 class AgenceAdminController extends AbstractMenuContent {


    private $request = null;
     /**
      * @var $sessionInterface SessionInterface
      */
    private $sessionInterface = null;
     /**
      * @var $currentAgence Agences
      */
    protected $currentAgence = null;
    private const LAST_AGENCE_LOGO_KEY = "lalk";
    private const LAST_AGENCE_ID_KEY = "laik";
    protected $currentTab = 0;
    private $mailer = null;

    public const ADD = 0;
    public const SET = 1;
    public const DELETE = 2;
    public const ACCES = 3;
     public const INVITATION = 4;

    /**
     *@Route("admin/agence", name="agence_admin")
     *
     * @param $session
     * @param $token
     * @param Request $request
     * @param $row
     * @param $mailer
     * @return $request
     */
     public function initAgenceAdmin(Request $request , SessionInterface $session  , $token = null , $row = null , \Swift_Mailer $mailer) {

        $this->request = $request;
        $this->sessionInterface = $session;
        $this->mailer = $mailer;

         parent::synchUserNAgence();
        /*
         * on initialise l'etat de la page cad l'agence selectionnee et l'onglet selectionne.e
         */
        $this->initPageState($request);


        $onglets = [
            "Ajouter d'agence"=>new MenuContentDefinition(self::ADD , false),
            "Modification d'agence"=>new MenuContentDefinition(self::SET , true , [
                parent::EVENT_TYPPE => FormEvents::POST_SUBMIT ,
                parent::EVENT_CALLBACK => $this->agenceListEventListener()
            ]),
            "Suppression d'agence"=>new MenuContentDefinition(self::DELETE , true , [
                parent::EVENT_TYPPE => FormEvents::POST_SUBMIT ,
                parent::EVENT_CALLBACK => $this->agenceListEventListener()
            ]),
            "Fournir les acces"=>$this->createAccessProviderDefinition(['token'=>$this->getSelectedRole($token) , 'id'=>$row]) ,
            "Envoyer une Invitation" =>$this->createInvitationDefinition()
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

        return parent::renderAllContent(
            $onglets ,
             $this->currentTab ,
              'agence_admin/agenceAdmin.html.twig' , $allow);

     }


    
    public function createMainForm(int $tabIndex): FormInterface {
        /*
         * cette methode peut etre definit comme bon vous semble tout depend
         * de comment vous proceder pour recuperer les donnees du contenu de votre onglet
         */
        switch($tabIndex) {
            case self::ACCES:
                //on prevoit le traitement pour les clonage.

            default:
                $form =  $this->createAgenceAdminForm($tabIndex);
                return $form;
        }

    }

    public function createSecondForm(int $tabIndex, array $eventData): FormInterface {
        // TODO: Implement createSecondForm() method.
        return $this->createDepartSideForm($tabIndex , $eventData);
    }

    /**
     *methode dupliquee on retrouve la meme dans DepartAdminController penser a utiliser un trait.
     *
     *
     *@param int  tab
     *@param array $eventData
     * @return FormInterface|null
     */
    public function createDepartSideForm(int $tab , array $eventData) {

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
                    'label'=>false,
                ]
            ]);
        $callableEventMethode = $eventData[parent::EVENT_CALLBACK];
        if(!is_null($callableEventMethode)) {
            $builder->get('agences')->addEventListener($eventData[parent::EVENT_TYPPE] , function (FormEvent $formEvent) use ($callableEventMethode){
                if(!is_null($callableEventMethode)) {
                    $callableEventMethode($formEvent);
                }
            });
        }

        $form = $builder->getForm();
        $form->handleRequest($this->request);
        
        return $form;
    }




    public function agenceListEventListener() : callable {
        return function(FormEvent $formEvent) {
            $this->currentAgence = $formEvent->getData();
            $parent = $formEvent->getForm()->getParent();
            /**
             * @var $selectedAgence Agences
             */
            $selectedAgence = $this->currentAgence;
            dump($selectedAgence);
            //car ce que l'on recupere n'est qe l'id de l'agence.
            $selectedAgence = $this->getDoctrine()->getRepository(Agences::class)->find($selectedAgence);
            $this->currentAgence = $selectedAgence;
        };
    }


    public function createAgenceAdminForm(?int $formType) {
        $block_name = array('add' , 'set' , 'del');
        $currentAgence = $this->currentAgence;
        $agence = is_null($currentAgence)
            ?new Agences()
            :$currentAgence;
        /**
         * @var $formFactory FormFactory
         * @var $agence Agences
         */
        $formFactory = $this->get('form.factory');
        $form = $formFactory->createNamed('agenceAdmin'.$formType , FormAgenceAdminType::class , $agence , ['block_name'=>$block_name[$formType]] );
        $form->handleRequest($this->request);
        if(!$form->isSubmitted()) {
            if($formType != self::ADD) {
                $this->sessionInterface->set(self::LAST_AGENCE_LOGO_KEY , $agence->getAgenceLogo());
            }
            if(!is_null($this->currentAgence)) {
                $form->get("id")->setData($this->currentAgence->getId());
            }
            $form->get("tab")->setData($formType);
        } else {
            if($form->isValid()) {
                dump($form);
                $this->currentTab = $formType;
                $agenceDirectory = $this->getParameter('agence_directory');
                if(is_null($agence->getAgenceLogo())) {
                    $agence->setAgenceLogo($this->sessionInterface->get(self::LAST_AGENCE_LOGO_KEY , AppConstants::AGENCE_DEFAULT_FILE_NAME));
                }
                /**
                 * @var $tmp UploadedFile
                 */
                $tmp = $agence->getAgenceLogo();
                if(is_null($tmp)) {
                    $agenceDirectory = null;
                }
                $handler = SubmitHandler::getInstance();

                switch ($formType) {
                    case self::ADD:
                        $handler->handleAgenceAdminForm($formType , $agence , $agenceDirectory , $this->getDoctrine());
                        break;
                    default:
                        /**
                         * @var $currentAgence Agences
                         */
                        $currentAgence = $agence;
                        dump($currentAgence);
                        $this->currentAgence = $currentAgence;
                        //pas de set Id alors on fait les choses differement.
                        if(!is_null($currentAgence) and !is_null($this->currentAgence)) {
                            $handler->handleAgenceAdminForm($formType , $currentAgence , $agenceDirectory , $this->getDoctrine());
                        }
                        break;
                }
                $this->extraInfo = ['message'=>'Operation effectuee avec succes :)' , 'type'=>'success'];
            }
        }

        return $form;
    }

    public function createInvitationDefinition() {
        if(!is_null($this->currentAgence)) {
            $agenceRepository = $this->getDoctrine()->getRepository(Agences::class);
            $this->currentAgence = $agenceRepository->find($this->currentAgence);
        }
        $invitation = new InvitationController();
        return $invitation->initInvitationSender(
            $this->createForm(FormInviteAdmin::class , new UnmappedForm()),
            $this->currentAgence ,
            $this->request ,
            $this->mailer , $this);
    }
     /**
      * methode permettant d'initialiser l'accessProviderController Dans cette class (le pont entre les deux controlleurs.)
      * @param $queryString
      * @return MenuContentDefinition
      */
    public function createAccessProviderDefinition($queryString) :MenuContentDefinition {

        $provider = new AccessProviderController();
        $form = $this->createForm(
            FormAccesProvider::class ,
            new UnmappedForm() ,
            ["isAdminAgence"=>$this->isAdminAgence , "user"=>$this->getUser()]);
        /**
         * @var NzelaUser $currentUser
         */
        $currentUser = $this->getUser();
        return $provider->initAccessProviders($currentUser , $queryString , $form , $this->getDoctrine() ,$this->sessionInterface, $this->request);
    }

    private function getSelectedRole($token) {
        $selectedRole = null;
        if(!is_null($token)) {
            $enrolledRole = $this->sessionInterface->get(AppConstants::ROLE_TOKENS , [] );
            $selectedRole = $enrolledRole[$token];
        }
        return $selectedRole;
    }

     /**
      * @Route("/agence/admin/set/{token}/{row}" , name = "accesOP")
      *
      * @param Request $request
      * @param SessionInterface $session
      * @param null $token
      * @param null $row
      * @param \Swift_Mailer $mailer
      * @return array
      */
    public function updateRole(Request $request , SessionInterface $session , $token = null , $row = null , \Swift_Mailer $mailer) {
        return $this->initAgenceAdmin($request , $session , $token , $row , $mailer);
    }

    public function initPageState(Request $request) {
        if(!is_null($request)) {
            $form = $request->request->get('form');
            if(!is_null($form)) {
                if(array_key_exists('tab' , $form)) {
                    $this->currentTab = $form['tab'];
                }
                if(array_key_exists('agences' , $form)) {
                    $this->sessionInterface->set(self::LAST_AGENCE_ID_KEY ,$form['agences']);
                    dump($this->currentAgence);
                }
            }

        }
        $this->currentAgence = $this->sessionInterface->get(self::LAST_AGENCE_ID_KEY , null);
    }

 }
