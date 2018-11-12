<?php
/**
 * Created by PhpStorm.
 * User: babygracias
 * Date: 9/29/2018
 * Time: 6:49 PM
 */

namespace App\Controller;


use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

interface AlertDialogInterface {

    /**
     * interface permettant a une page d'implementer une boite de dialog
     * @return mixed
     */
    public function setAlertForm();

    /**
     * permet de definir le contenue de la boite de dialog le tring qui sera echo ou affiche via twig
     *
     * @param string $alertStringContent
     * @return mixed
     */
    public function setAlertStringContent(string $alertStringContent);

}