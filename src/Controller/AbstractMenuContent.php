<?php
/**
 * Created by PhpStorm.
 * User: babygracias
 * Date: 9/14/2018
 * Time: 9:23 PM
 */

namespace App\Controller;


use App\Entity\NzelaUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractMenuContent extends AbstractController{

    protected $currentTab = 0;
    const TAB_KEY = 'dataTab';
    const TAB_CONTENT = 'dataContent';
    const EVENT_TYPPE = 'type';
    const EVENT_CALLBACK = 'callback';
    public const EXTRA_JS_KEY = "js";
    protected $currentAgence = null;
    protected $isAdminAgence = false;
    protected $extraInfo = ['message'=>'' , 'type'=>''];
    protected $js = null;

    /**
     * methode a appeler a la fin de traitement de la route.
     *
     * @param array $ongletsName
     * @param int $currentTab
     * @param string templateName
     * @param $allow
     * @param $extra pour passer du js ou tout information generque de la page
     * @return Response
     */
    public function renderAllContent(array $ongletsName , int $currentTab , string $templateName , bool $allow , $extra = null):Response {

        $this->currentTab = $currentTab;
        $onglets = [];

        foreach ($ongletsName as $key=>$tab) {
            /**
             * @var $tab MenuContentDefinition
             */

            $tmp = [
                'sideForm'=>$tab->isSideContent()
                    ?$this->createSecondForm($tab->getTab() , $tab->getEventData())->createView()
                    :$tab->getData(),
                'centralForm'=>$tab->isMainContent()
                    ?$this->createMainForm($tab->getTab())->createView()
                    :$tab->getData()
            ];
            $onglets[$key] = $tmp;
        }
        $extra[self::EXTRA_JS_KEY] = $this->js;
        $extra['extraInfo'] = $this->extraInfo;
        dump($extra);
        return
            $this->render($templateName ,  [
                'page'=>'departAdmin',
                'onglets' => $onglets,
                'currentTab' => $this->currentTab,
                'allow'=>$allow,
                'extra'=>$extra,
            ]);


    }

    /**
     * methode permetant d'initialiser l'agence courant en tenant compte du fait que l'utilisateur connecte.e est un admin d'agence ou pas
     * cette methode permettra par la suite grace a isAdminAgence de chrger toutes les agences ou rien que l'agence concernee
     */
    protected function synchUserNAgence() {

        /**
         * @var $user NzelaUser
         */
        $user = $this->getUser();
        $this->currentAgence = $user->getIdAgence();
        $this->isAdminAgence = !is_null($this->currentAgence);


    }


    /**
     * c'est dans cette methode qu'est cree le contenu principal de l'onglet
     * cette methode est prevu pour les cas il y a des formulaire a creer pour le contenu principale de l'onglet.
     *
     * @param int $tabIndex
     * @return FormInterface
     */
    public abstract function createMainForm(int $tabIndex):FormInterface;

    /**
     * et ici le contenu secondaire de l'onglet
     * meme chose que la methode precedente sauf qu'ici c'est pour tout ce qui concerne le secondaire les formulaire de controle.
     *
     *
     * @param int $tabIndex
     * @param array $eventData
     * @return FormInterface
     */
    public abstract function createSecondForm(int $tabIndex, array $eventData):FormInterface;


    /**
     * methode permettant de definir l'etat d'une page quand elle est chargee ce ci en utlisant l'objet Request
     * @param Request $request
     * @return mixed
     */
    public abstract function initPageState(Request $request);

}