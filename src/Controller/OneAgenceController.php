<?php

namespace App\Controller;

use App\Entity\Agences;
use App\Entity\Commentaires;
use App\Entity\Departs;
use App\Entity\NzelaUser;
use App\Form\CommentaireType;
use App\Repository\AgencesRepository;
use App\Repository\CommentairesRepository;
use App\Repository\DepartsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;


class OneAgenceController extends AbstractController
{
    /**
     * @Route("/one/agence/{id}", name="one_agence")
     * @Entity("agence", expr="repository.find(id)")
     *
     */
    public function index(Agences $agence, Request $request)
    {

        //Formulaire des commentaire
        $commentaire = new Commentaires();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);
        if ($request->isMethod('POST')) {
            if ($form->isValid()) {
                $manager = $this->getDoctrine()->getManager();
                $commentaire->setAgence($agence);
                $manager->persist($commentaire);
                $manager->flush();
                return new RedirectResponse($this->generateUrl("one_agence", ['id' => $agence->getId()]));
            }
        }

        $departHandler = DepartHandler::getGlobalInstance();

        /**
         * @var $repository DepartsRepository
         */
        $repository = $this->getDoctrine()->getRepository(Departs::class);

        $departsInfo = $departHandler->getDepartByAgence($agence, $repository);
        //on recuperera les commentaires apres.
        $buider = $this->createFormBuilder(new UnmappedForm())
            ->add("tansactions", SubmitType::class,
                ["attr" => [
                    "class" => "btn btn-outline-light",
                    "value" => "liste des transactions"
                ]]);
        $buider->setAction($this->generateUrl("summary"));

        //O recupere les commentaire
            /**
             * @var $commentRepository CommentairesRepository
             */
            $commentRepository = $this->getDoctrine()->getRepository(Commentaires::class);
            $commentaires = $commentRepository->findByAgence($agence);

        /**
         * @var $user NzelaUser
         */
        $user = $this->getUser();

        return $this->render('one_agence/oneAgence.html.twig', [
            "page" => "oneAgence",
            "agence" => $agence,
            "departs" => $departsInfo[0],
            "dateTitle" => $departsInfo[1],
            "departActionForm" => $buider->getForm()->createView(),
            "commentaires" => $commentaires,
            "form" => $form->createView(),
            //list allowed est le boolean permettat de savoir si l'utilisateur connecte a
            // la possibilite de voire les differentes transactions effectuee.
            "listAllowed"=> $this->isGranted(AppConstants::ROLES[2])
                ? true
                :is_null($user)
                    ? false
                    :($user->getIdAgence()->getId() === $agence->getId())
                        ? true
                        : false
        ]);
    }

}
