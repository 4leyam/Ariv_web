<?php
/**
 * Created by PhpStorm.
 * User: babygracias
 * Date: 9/2/2018
 * Time: 12:45 PM
 */

namespace App\Controller;


use App\Entity\Agences;
use App\Entity\NzelaUser;
use phpDocumentor\Reflection\Types\Self_;
use Symfony\Component\Security\Core\User\User;

class AppConstants {

    public const AppName = "ARIV";
    public const ROLE_USER = "ROLE_USER";
    public const ROLE_COMPAGNY_ADMIN = "ROLE_COMPAGNY_ADMIN";
    public const ROLE_AGENCE_ADMIN = "ROLE_AGENCE_ADMIN";
    public const ROLE_APP_OPERATOR_ADMIN = "ROLE_APP_OPERATOR_ADMIN";
    public const ROLE_SUPER_ADMIN = "ROLE_YOUNG_POPE";
    public const SESS_isLoginError = 'false';
    public const DEPART_DEFAULT_FILE_NAME = 'bus.png';
    public const AGENCE_DEFAULT_FILE_NAME = 'rt_b.jpg';
    public const ROLE_TOKENS = "roles_tokens";
    //public const SESS_FIlterDes
    //le tableau permet de connaitre la hierarchie des roles et donc de faire des comparaisons hierarchiques.
    public const ROLES = [
        self::ROLE_USER,
        self::ROLE_AGENCE_ADMIN,
        self::ROLE_COMPAGNY_ADMIN,
        self::ROLE_APP_OPERATOR_ADMIN,
        self::ROLE_SUPER_ADMIN];


    public static function getObjectAuthorization(NzelaUser $user , Agences $agence) : bool {
       return $user->getIdAgence()->getId() == $agence->getId();
    }

}