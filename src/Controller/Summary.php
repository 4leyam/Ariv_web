<?php
/**
 * Created by PhpStorm.
 * User: babygracias
 * Date: 9/2/2018
 * Time: 12:32 PM
 */

namespace App\Controller;


class Summary {

    private $summaryMessage = "";
    private $commentMessage ="";
    private $AgenceMessage = "";



    public function __construct($summaryMessage = null,
                                $commentMessage = null,
                                $AgenceMessage = null) {
        if(is_null($summaryMessage)) {
            $commentMessage = "desormais depuis votre domicile, ou que ce soit votre position, vous n'etes plus oblige de vous
        deplacer
        pour l'achat de billets de voyage a l'interieur du pays. ".AppConstants::AppName." vous permet de faire des
        reservations
        et des achats de billets de trains, d'avion et de route.";
        }
    }


    /**
     * @return string
     */
    public function getSummaryMessage(): string {
        return $this->summaryMessage;
    }

    /**
     * @return string
     */
    public function getAgenceMessage(): string {
        return $this->AgenceMessage;
    }

    /**
     * @return string
     */
    public function getCommentMessage(): string {
        return $this->commentMessage;
    }

}