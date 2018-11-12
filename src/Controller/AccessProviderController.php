<?php
/**
 * Created by PhpStorm.
 * User: babygracias
 * Date: 9/24/2018
 * Time: 8:18 PM
 */

namespace App\Controller;


use App\Entity\NzelaUser;
use App\Repository\NzelaUserRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class AccesControlController
 * @package App\Controller
 */

class AccessProviderController {

    private $accessDefinition = null;
    /**
     * @var ManagerRegistry $doctrine
     */
    private $doctrine = null;
    /**
     * @var NzelaUser $currentUser
     */
    private $currentUser = null;

    /**
     * methode permettant d'initialiser et de mettre en place toutes les donnees pour
     * l'onglet du fournissage d'acces.
     *
     * @param FormInterface $form qui doit etre un formulaire utilisant le ty FormAccesProvider.
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param $queryString {qui n'est autre que l'ensemble des parametre resultant du changement d'acces}
     * @param NzelaUser $currentUser
     * @param SessionInterface $session
     * @return MenuContentDefinition
     */
    public function initAccessProviders(NzelaUser $currentUser ,
                                        array $queryString ,
                                        FormInterface $form ,
                                        ManagerRegistry $doctrine ,
                                        SessionInterface $session,
                                        Request $request) : MenuContentDefinition {

        $this->doctrine = $doctrine;
        $this->currentUser = $currentUser;
        /**
         * @var MenuContentDefinition $accessDefinition
         */
        $accessDefinition = new MenuContentDefinition(
            AgenceAdminController::ACCES ,
            false , null ,
            false);
        $email = null;
        $form->handleRequest($request);
        //aux cas ou un changement de role a ete fait on applique la modification.
        $this->updateUserRole($queryString);
        if($form->isSubmitted() and $form->isValid()) {
            $email =  $request->request->get('form_acces_provider')['email'];
        }

        $accesData = [];
        $accesData[AbstractMenuContent::TAB_KEY ] = $accessDefinition->getTab();
        //status ou authorisation actuelle.
        //authorisations on a les authorisation que peut partager l'utilisateur courant.
        $roles = $currentUser->getRole();
        $accesData[AbstractMenuContent::TAB_CONTENT] = array(
            "headList" => ["Nom" , "Prenom" , "e-mail" , "status" , "Authorisation"],
            "authorisations"=>$roles,
            "form"=>$form->createView(),
            "rolesTokens"=>$this->getRoleToken($roles , $session),
            "users" => is_null($email)
                ? $this->fetchAllAdmins()
                :$this->fetchConcernedUserList($email),
        );
        $accessDefinition->setData($accesData);

        return $accessDefinition;

    }

    private function updateUserRole(array $queryString) {
        if(!is_null($queryString) and sizeof($queryString) > 0) {

            if(!is_null($queryString["token"]) and !is_null($queryString["id"])) {
                dump($queryString["token"]);
                dump($queryString["id"]);

                /**
                 * @var NzelaUserRepository $userRepository
                 */
                $userRepository = $this->doctrine->getRepository(NzelaUser::class);
                $user = $userRepository->find($queryString["id"]);
                $user->setRole($queryString["token"]);
                $manager = $this->doctrine->getManager();
                $manager->persist($user);
                $manager->flush();
            }
        }
    }


    private function getRoleToken(array $roles , SessionInterface $session):array {
        $enrolledRole = [];
        $rolesTokens = [];
        for ($i = 0 ; $i<sizeof($roles) ; $i++) {
            $rolesTokens[$i] = md5(uniqid());
            $enrolledRole[$rolesTokens[$i]] = array_search($roles[$i] , AppConstants::ROLES);
        }
        $session->set(AppConstants::ROLE_TOKENS , $enrolledRole);
        return $rolesTokens;
    }

    /**
     * methode permettant de recuperer l'utilisateur dont le mail correspond.
     * @param string $email
     * @return NzelaUser[]
     */
    private function fetchConcernedUserList(string $email): array {
        /**
         * @var NzelaUserRepository $userRepository
         */
        $userRepository = $this->doctrine->getRepository(NzelaUser::class);
        $agence = $this->currentUser->getIdAgence();
        $role = $this->currentUser->getRoles()[0];
        return $userRepository->getConcernedUser($role , $agence , $email);

    }

    private function fetchAllAdmins() {
        /**
         * @var NzelaUserRepository $userRepository
         */
        //erreur de frappe lors de la Creation IdAgence enfait c'est Agence y'a qu'a regarder ce que la fonction retourne.
        $userRepository = $this->doctrine->getRepository(NzelaUser::class);
        $agence = $this->currentUser->getIdAgence();
        $role = $this->currentUser->getRoles()[0];
        return $userRepository->getAdminUnderRole($role , $agence);
    }

}