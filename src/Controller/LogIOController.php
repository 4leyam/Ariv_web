<?php

namespace App\Controller;

use App\Entity\InvitationTokens;
use App\Entity\NzelaUser;
use App\Form\FormAddUser;
use App\Form\FormLogin;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Tests\Encoder\PasswordEncoder;

class LogIOController extends AbstractController
{
    /**
     * @Route("/logIO", name="logIO")
     */
    public function index(Request $request , SessionInterface $session)
    {

        $error = $session->get(AppConstants::SESS_isLoginError , false);
        $user = new NzelaUser();
        $form = $this->createForm(FormLogin::class , $user);
        $form->handleRequest($request);

        if($form->isSubmitted()) {
            if(!$this->isGranted(AppConstants::ROLES[0])) {
                $error = true;
            }
        }

        return $this->render('log_io/login.html.twig', [
            'page' => 'Connexion',
            'title' => 'Connectez vous afin de beneficier de plus de fonctionnalites',
            'form' => $form->createView(),
            'error' =>$error
        ]);
    }

    /**
     * @Route("/out" , name="deconnexion")
     */
    public function logout(){

    }

    /**
     * @Route("/logIO/failed" , name="logFailed")
     */
    public function logFailed(SessionInterface $sessI){
    //si l'authentification a echouee.
        $sessI->set(AppConstants::SESS_isLoginError , true);
       return $this->redirectToRoute('logIO');
    }


    /**
     * @Route("/add/{token}", name="addUser")
     * @param Request $request
     * @param ObjectManager $ojm
     * @param UserPasswordEncoderInterface $upei
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addUser(Request $request , ObjectManager $ojm , UserPasswordEncoderInterface $upei , string $token) {
        $error = false;
        $user = new NzelaUser();
        $form = $this->createForm(FormAddUser::class , $user);
        $form->handleRequest($request);
        /**
         * @var $invitation InvitationTokens
         */
        $invitations = $this->getDoctrine()->getRepository(InvitationTokens::class)->findBy(["token"=>$token]);
        if(sizeof($invitations) > 0){
            //si le token est bien correct
            $invitation = $invitations[0];
            $invitationEmail = $invitation->getEmailId();
            if($form->isSubmitted()) {
                if($form->isValid()) {
                    if($invitationEmail === $user->getEmaiId()){
                        $mdp = $user->getPassword();
                        $user->setPassword($upei->encodePassword($user , $mdp));
                        $user->setRole(1);
                        $user->setIdAgence($invitation->getAgenceId());
                        $ojm->persist($user);
                        $ojm->flush();
                        return $this->redirectToRoute('agences');
                    } else {
                        $error = true;
                    }
                } else {
                    $error = true;
                }
            }
        }


        return $this->render('log_io/add_user.html.twig' , [
            'page' => 'Connexion',
            'title' => 'faite la Difference rejoignez la grande famille ARIV',
            'form' => $form->createView(),
            'error' =>$error
        ]);

    }
}
