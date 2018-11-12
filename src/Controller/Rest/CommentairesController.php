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
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @param Request $request
     * @return Commentaires
     */
    public function postAction(Request $request) {

        $newComment = new Commentaires();
        /**
         * @var $agence Agences
         */
        $agence =  $this->doctrine->getRepository(Agences::class)
            ->find($request->get("Agence_Id"));

        //TODO Validation de donnees entrant, comme l'api n'est pas public la validation du client fait l'affaire.


        $newComment->setAgence($agence);
        $newComment->setUserName($request->get("userName"));
        $newComment->setAvis($request->get("avis"));
        $newComment->setCommentaire($request->get("Commentaire"));
        $manager = $this->doctrine->getManager();
        $manager->persist($newComment);
        $manager->flush();
        return $newComment;

    }

}