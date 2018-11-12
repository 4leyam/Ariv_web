<?php

namespace App\DataFixtures;

use App\Entity\Agences;
use App\Entity\Departs;
use App\Entity\Location;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Validator\Constraints\Date;

class AppFixtures extends Fixture
{

    public function load(ObjectManager $manager )
    {

        $origine = new Location("Brazzaville" , "Congo");
        $destination = new Location("Noida" , "Inde");
        $manager->persist($origine);
        $manager->persist($destination);

        for($i = 0 ; $i<30 ; $i++) {
            $oneAgence = new Agences();
            $oneAgence->setNomAgence("Agence ".$i)
                ->setAgenceLogo("rt_b.jpg")
                ->setAdresseAgence("Adresse ".$i)
                ->setAvis(mt_rand(0 , 5))
                ->setEmailAgence("aleyamSlts".$i."@hotmail.com")
                ->setContactAgence("contact ".$i)
                ->setAdresseAgence($i." Rue Kimpwandza")
                ->setPlusInfo("Contrairement à une opinion répandue, le Lorem Ipsum n'est pas simplement du 
                texte aléatoire. Il trouve ses racines dans une oeuvre de la littérature latine classique datant de 45 av. J.-C., 
                le rendant vieux de 2000 ans. Un professeur du Hampden-Sydney College, en Virginie, s'est intéressé à un des mots latins
                 les plus obscurs, consectetur, extrait d'un passage du Lorem Ipsum, et en étudiant tous les usages de ce mot dans la littérature
                  classique, découvrit la source incontestable du Lorem Ipsum. Il provient en fait des sections 1.10.32 et 1.10.33 du \"De Finibus Bonorum 
                  et Malorum\" (Des Suprêmes Biens et des Suprêmes Maux) de Cicéron. Cet ouvrage, très populaire pendant la Renaissance, 
                  est un traité sur la théorie de l'éthique. Les premières lignes du Lorem Ipsum, \"Lorem ipsum dolor sit amet...\", 
                  proviennent de la section 1.10.32.
                    L'extrait standard de Lorem Ipsum utilisé depuis le XVIè siècle est reproduit ci-dessous
                     pour les curieux. Les sections 1.10.32 et 1.10.33 du \"De
                     Finibus Bonorum et Malorum\" de Cicéron sont aussi reproduites dans leur version originale, 
                     accompagnée de la traduction anglaise de H. Rackham (1914).");

            for($j = 0 ; $j<($i/2)+1 ; $j++) {
                $depart = new Departs();
                $depart->setFormalite(new \DateTime('+ '.$j.' day'));
                $depart->setDateDepart(new \DateTime('+ '.$j.' day'));
                $depart->setDestination($destination);
                $depart->setOrigine($origine);
                $depart->setAgence($oneAgence);
                $depart->setImageBus("bus.png");
                $depart->setPlaceRestante($j)
                    ->setPlaceInit($i)
                    ->setTarifAdult($i*100)
                    ->setTarifEnfant($j*100)
                    ->setValide(true)
                    ->setDepart(new \DateTime('+ '.$i.' day'));
                $manager->persist($depart);
            }
            $manager->persist($oneAgence);
        }

        $manager->flush();
    }
}
