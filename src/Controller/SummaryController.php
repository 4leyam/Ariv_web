<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SummaryController extends AbstractController
{
    /**
     * @Route("/", name="summary")
     */
    public function index() {
        //on affiche le message d'Acceuil
        return $this->render('summary/appSummary.html.twig', [
            'page' => 'acceuil',
        ]);
    }
}
