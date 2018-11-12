<?php
namespace App\Controller;

use App\Entity\Agences;
use App\Entity\Departs;
use App\Repository\DepartsRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class DepartHandler {

    private static $instance = null;
    private $curentAgence = null;


    public static function getGlobalInstance() {
        if(is_null(self::$instance)) {
            self::$instance = new DepartHandler(null);
        }
        return self::$instance;
    }

    public static function getInstance($agence) {
        if(is_null(self::$instance)) {
            self::$instance = new DepartHandler($agence);
        }
        return self::$instance;
    }

    private function __construct(?Agences $curentAgence) {
        if(!is_null($curentAgence)) {
            $this->curentAgence = $curentAgence;
        }
    }

    public function getDepartByAgence(Agences $agence , DepartsRepository $dr , SessionInterface $sess = null ): array {
        /**
         * @var $departs Departs
         */
        $departs = $agence->getDeparts();
        return $this->getOrderedDepart($departs , $dr , $sess);
    }

    public function organizeDep(array $departs , DepartsRepository $dr) {
        return $this->getOrderedDepart($departs , $dr);
    }

    public function getDepartByPeriod(Agences $agence , string $periode , DepartsRepository $dr , SessionInterface $sess = null ):array {
        $mois = DepartsRepository::$mois;
        $filter_tab = explode('-' , $periode);
        $mois = array_search($filter_tab[0] , $mois)+1;
        $moisX = $mois + 1;
        $annee = $filter_tab[1];
        $anneeX = $filter_tab[1];
        if($mois == 12) {
            $moisX = 1;
            $anneeX++;
        }
        $min = $annee.'-'.($mois).'-1';
        $max = $anneeX.'-'.($moisX).'-1';

        $departs =  $dr->getCustomDepart($agence , $min , $max);


        return $this->getOrderedDepart($departs , $dr , $sess , true);

    }

    private function getOrderedDepart($departs , DepartsRepository $dr , SessionInterface $sess = null , ?bool $tabHistory = false ) {
        //on commence par recuperer les departs et ensuite on les classes


        $index = null;
        $dateTitle = [];
        $orderedDepartures = [];

        $cached_departs = [];
        setlocale(LC_TIME, "FR");
        for ($i = 0; $i < sizeof($departs) ; $i++) {
            /**
             * depart correspond a un enregistrement de depart.
             * @var $depart Departs
             */
            $depart = $departs[$i];
//            $depart->setDepart(new \DateTime($depart->getDepart()->format('h:i:s')));
            $depart->setDateDepart(new \DateTime($depart->getDateDepart()->format('Y-m-d')));
//            $depart->setFormalite(new \DateTime($depart->getFormalite()->format('h:i:s')));
            //externId est un id canular qui passera via l'url
            $depart->extern_id = ''.uniqid(''.uniqid());
            //avant toute operation on verifie si le depart est toujours d actualite.
            $date_bd = new \DateTime($depart->getDateDepart()->format('Y-m-d'));
            $today = new \DateTime('');
            // on conserve l'objet cached depart en session afin que ce dernier soit reutilise autre part dans l'app.
            $cached_departs[$depart->extern_id] = $depart;


            //TODO revoire la comparaison des date afin de permettre aux users de faire des transactions le jours meme de son expiration si y'a encore des places
            if ( $date_bd == $today or $date_bd > $today or $tabHistory) {
                $found = false;

                if ($i != 0) {

                    foreach ($dateTitle as $depart_date) {
                        if (strftime('%A le %d', $date_bd->getTimestamp()) == $depart_date) {

                            $orderedDepartures[strftime('%A le %d', $date_bd->getTimestamp())][] = $depart;
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {

                        $dateTitle[] = strftime('%A le %d', $date_bd->getTimestamp());
                        $orderedDepartures[strftime('%A le %d', $date_bd->getTimestamp())][] = $depart;
                    }
                } else {

                    $dateTitle[] = strftime('%A le %d', $date_bd->getTimestamp());
                    //on recupere les dates et on les mets dans le tableau des titres.
                    $orderedDepartures[strftime('%A le %d', $date_bd->getTimestamp())][] = $depart;
                }
            } else {
                $dr->desableDepart($depart->getId());
            }

        }

        if(!is_null($sess)) {
            $sess->set('cached_dep' , $cached_departs);
        }
        return array($dateTitle, $orderedDepartures);
    }
}