<?php
/**
 * Created by PhpStorm.
 * User: ryans
 * Date: 07-11-2018
 * Time: 19:40
 */

namespace App\Controller\Rest;


use App\Entity\Agences;
use App\Repository\AgencesRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Serializer\Annotation\Groups;



/**
 * Class AgenceController
 * gere la recuperation des ressources en auto-generant les url
 * en fonction des actions pouvant etre effectuees sur l'objet agence.
 *
 * @package App\Controller\Rest
 */
class AgenceController extends Controller implements ClassResourceInterface
{

    private const POPULAR = "popular";
    private const OLD = "old";
    private const NEW = "new";

    /**
     * @var $doctrine AgencesRepository
     */
    private $agenceRepository= null;
    private $data = null;


    public function __construct(ManagerRegistry $doctrine)
    {
        $this->agenceRepository = $doctrine->getRepository(Agences::class);
    }


    /**
     * ClassResourceInterface est une interface permettant de
     * generer les differentes url en fonction des actions de chaques methode que comprends la class
     */

    /**
     * @Rest\View(serializerGroups={"self"})
     * traite la recuperation de toutes les agences
     * @param $filter
     * @return null
     */
    public function cgetAction(string $filter) {

        switch ($filter) {
            case self::POPULAR:
                $this->data = $this->agenceRepository->findTop();
                break;
            case self::OLD:
                $this->data = $this->agenceRepository->findOlds();
                break;
            case self::NEW:
                $this->data = $this->agenceRepository->findRecents();
                break;
        }
        return $this->data;

    }

    /**
     * recupere les departs d'une agence
     *
     * @Rest\View()
     * @param $slug
     * @return Agences|\Exception
     */
    public function getMoreAction($slug) {

        if(is_numeric($slug)) {

            $this->data = $this->agenceRepository->find($slug);
            $this->data->setComments(null);
            foreach ($this->data->getDeparts() as $depart) {
                if(!$depart->getValide()) {
                    //on ne renvoie que les departs qui sont encore d'actu
                    $this->data->removeDepart($depart);
                }
            }
            return $this->data;
        } else {
            return new \Exception("the slug should be a number");
        }

    }

    /**
     * @Rest\View(serializerGroups={"self"})
     * @param $slug
     * @return Collection|null
     */
    public function getCommentairesAction($slug) {
        $agence = $this->agenceRepository->find($slug);
        return $agence->getComments();
    }

    //les action en dessous ne sont pas encore pris en compte car aucun type de client n'a le droit de les effectuer.

    /**
     * ajoute une nouvelle agence dans la base de donnee
     *
     * @param $slug
     */
    public function postAction($slug) {

    }


    /**
     * met a jour une Agence
     * @param $slug
     */
    public function putAction($slug) {

    }

    /**
     * supprime une agence
     * @param $slug
     */
    public function deleteAction($slug) {

    }


}