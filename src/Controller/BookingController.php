<?php
/**
 * Created by PhpStorm.
 * User: babygracias
 * Date: 9/22/2018
 * Time: 8:46 PM
 */

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController {



    /**
     * @Route("/check/bookings", name="bookings")
     *
     */
    public function initList(Request $request) {

        $idDepart = $request->request->get('idDepart');

        return $this->render("booking/bookingList.html.twig" ,
                      [
                          'page'=>'Liste des Transactions' ,
                          'idDepart'=>$idDepart
                      ]);

    }

}