<?php
/**
 * Created by PhpStorm.
 * User: ryans
 * Date: 08-11-2018
 * Time: 10:09
 */

namespace App\Controller\Rest;


use App\Entity\Departs;
use App\Repository\DepartsRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;

class DepartsController extends Controller implements ClassResourceInterface
{


    /**
     * @var $departRepository DepartsRepository
     */
    private $departRepository = null;
    private $data = null;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->departRepository = $doctrine->getRepository(Departs::class);

    }

    /**
     * @Rest\View()
     * methode permettant de recuperer un depart en particulier.
     */
    public function getAction($slug) {
        if(is_numeric($slug)) {

            $this->data = $this->departRepository->find($slug);
            $agence = $this->data->getAgence();

            $agence->setDeparts(null);
            $agence->setNzelaUsers(null);
            $agence->setInvitationTokens(null);
            $agence->setComments(null);

            return $this->data;

        } else {
            return new Exception("the slug should be a number");
        }

    }



}







