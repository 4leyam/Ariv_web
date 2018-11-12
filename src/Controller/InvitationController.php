<?php
/**
 * Created by PhpStorm.
 * User: babygracias
 * Date: 9/26/2018
 * Time: 6:14 PM
 */

namespace App\Controller;


use App\Entity\Agences;
use App\Entity\InvitationTokens;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class InvitationController extends AbstractController {


    /**
     * @var $c AbstractController
     */
    private $c = null;


    public function initInvitationSender(FormInterface $form , ?Agences $currentAgence , Request $request ,\Swift_Mailer $mailer , AgenceAdminController $c) {
        $this->c = $c;
        $form->handleRequest($request);
        if($form->isSubmitted() and $form->isValid()) {
            $this->sendInvitationMail($currentAgence->getNomAgence() , $form->get('email')->getData() , $mailer , $currentAgence);
        }

        $inviteDefinition = new MenuContentDefinition(AgenceAdminController::INVITATION , true , [
            AbstractMenuContent::EVENT_TYPPE => null ,
            AbstractMenuContent::EVENT_CALLBACK => null
        ] , false);

        $inviteData = [];
        $inviteData[AbstractMenuContent::TAB_KEY ] = $inviteDefinition->getTab();
        //status ou authorisation actuelle.
        //authorisations on a les authorisation que peut partager l'utilisateur courant.
        $inviteData[AbstractMenuContent::TAB_CONTENT] = array(
            "form"=>$form->createView(),
        );
        $inviteDefinition->setData($inviteData);

        return $inviteDefinition;
    }

    private function sendInvitationMail(string $compagny , string $email , \Swift_Mailer $mailer , Agences $agence) {


        $message = (new \Swift_Message('ARIV invitation'))
            ->setFrom('noreplyariv@gmail.comm')
            ->setTo($email)
            ->setBody(
                $this->c->renderView(
                    'agence_admin/invitation.html.twig',
                    [
                        "Etoken"=>$this->createEmailToken($email , $agence),
                        "compagnie"=>$compagny,
                        "page"=>'Invitation'
                    ]
                ),
                'text/html'
            );

        $mailer->send($message);


    }

    private function createEmailToken(string $email , Agences $agence) {
        $token = rtrim(strtr(base64_encode(md5(uniqid(uniqid()))), '+/', '-_'), '=');
        $invitationToken = new InvitationTokens();
        $invitationToken->setEmailId($email);
        $invitationToken->setToken($token);
        $invitationToken->setAgenceId($agence);
        $manager = $this->c->getDoctrine()->getManager();
        $manager->persist($invitationToken);
        $manager->flush();
        dump($token);
        return $token;

    }

//    function base64url_decode($data) {
//        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
//    }
//    $token = strstr(base64_encode(random_bytes(9)) , '+/' , '-_');
}