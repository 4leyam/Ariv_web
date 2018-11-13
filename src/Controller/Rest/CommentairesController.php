<?php
/**
 * Created by PhpStorm.
 * User: ryans
 * Date: 08-11-2018
 * Time: 10:08
 */

namespace App\Controller\Rest;


use App\Entity\Agences;
use App\Entity\Commentaires;
use Doctrine\Common\Persistence\ManagerRegistry;
use FOS\RestBundle\FOSRestBundle;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Response;

class CommentairesController extends FOSRestBundle implements ClassResourceInterface
{

    private $doctrine = null;

    public function __construct(ManagerRegistry $doctrine )
    {
        $this->doctrine = $doctrine;
    }

    /**
     *
     * @param Request $request
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @return Commentaires
     */
    public function postAction(Request $request) {

        $newComment = new Commentaires();
        /**
         * @var $agence Agences
         */
        $agence =  $this->doctrine->getRepository(Agences::class)
            ->find($request->get("agence_id"));

        //TODO Validation de donnees entrant, comme l'api n'est pas public la validation du client fait l'affaire.


        $newComment->setAgence($agence);
        $newComment->setUserName($request->get("userName"));
        $newComment->setAvis($request->get("avis"));
        $newComment->setCommentaire($request->get("commentaire"));
        $manager = $this->doctrine->getManager();
        $manager->persist($newComment);
        $manager->flush();
        $agenceOfNewComment = $newComment->getAgence();
        $agenceOfNewComment->setDeparts(null);
        $agenceOfNewComment->setComments(null);
        $agenceOfNewComment->setNzelaUsers(null);
        $agenceOfNewComment->setInvitationTokens(null);
        $newComment->setAgence($agenceOfNewComment);
        return $newComment;

    }

}