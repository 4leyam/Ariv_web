<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="App\Repository\DepartsRepository")
 */
class Departs
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="time")
     */
    private $formalite;

    /**
     * @ORM\Column(type="time")
     */
    private $depart;

    /**
     * @ORM\Column(type="integer")
     */
    private $placeInit;

    /**
     * @ORM\Column(type="integer")
     */
    private $tarifAdult;

    /**
     * @ORM\Column(type="integer")
     */
    private $tarifEnfant;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateDepart;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\File(
     *     maxSize = "1024k",
     *     mimeTypes = {"image/jpeg", "image/png"},
     *     mimeTypesMessage = "Entrez une image au bon format s'il vous plait"
     * )
     */
    private $imageBus;

    /**
     * @ORM\Column(type="boolean")
     */
    private $valide;

    /**
     * @ORM\Column(type="integer")
     */
    private $placeRestante;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Agences", inversedBy="departs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $agence;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Location", inversedBy="departs")
     * @ORM\JoinColumn(nullable=false)
     * @var Location $origine
     */
    private $origine;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Location")
     * @ORM\JoinColumn(nullable=false)
     * @var Location $destination
     */
    private $destination;


    public function __construct()
    {
        $this->localisations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id) {
        $this->id = $id;
    }

    public function getFormalite(): ?\DateTimeInterface
    {
        return $this->formalite;
    }

    public function setFormalite(?\DateTimeInterface $formalite): self
    {
        $this->formalite = $formalite;

        return $this;
    }

    public function getDepart(): ?\DateTimeInterface
    {
        return $this->depart;
    }

    public function setDepart(?\DateTimeInterface $depart): self
    {
        $this->depart = $depart;

        return $this;
    }

    public function getPlaceInit(): ?int
    {
        return $this->placeInit;
    }

    public function setPlaceInit(int $placeInit): self
    {
        $this->placeInit = $placeInit;

        return $this;
    }

    public function getTarifAdult(): ?int
    {
        return $this->tarifAdult;
    }

    public function setTarifAdult(int $tarifAdult): self
    {
        $this->tarifAdult = $tarifAdult;

        return $this;
    }

    public function getTarifEnfant(): ?int
    {
        return $this->tarifEnfant;
    }

    public function setTarifEnfant(int $tarifEnfant): self
    {
        $this->tarifEnfant = $tarifEnfant;

        return $this;
    }

    public function getDateDepart(): ?\DateTimeInterface
    {
        return $this->dateDepart;
    }

    public function setDateDepart(?\DateTimeInterface $dateDepart): self
    {
        $this->dateDepart = $dateDepart;

        return $this;
    }

    public function getImageBus()
    {
        return $this->imageBus;
    }

    public function setImageBus($imageBus): self
    {
        $this->imageBus = $imageBus;

        return $this;
    }

    public function getValide(): ?bool
    {
        return $this->valide;
    }

    public function setValide(bool $valide): self
    {
        $this->valide = $valide;

        return $this;
    }

    public function getPlaceRestante(): ?int
    {
        return $this->placeRestante;
    }

    public function setPlaceRestante(int $placeRestante): self
    {
        $this->placeRestante = $placeRestante;

        return $this;
    }

    public function getAgence(): ?Agences
    {
        return $this->agence;
    }

    public function setAgence(?Agences $agence): self
    {
        $this->agence = $agence;

        return $this;
    }

    public function getOrigine(ManagerRegistry $managerRegistry = null , bool $getObject = false)
    {


        if($this->origine instanceof Location) {
            return is_null($this->origine)
                ?"--"
                :$getObject
                    ?$this->origine
                    :$this->origine->getVille();
        } else if(!is_null($this->origine) and !is_null($managerRegistry)) {
            return $getObject
                ? $this->findLocationByVille($managerRegistry , $this->origine)
                : $this->findLocationByVille($managerRegistry , $this->origine)->getVille();
        } else {
            return $this->origine;
        }

    }

    public function setOrigine($origine , ?ManagerRegistry $managerRegistry = null ): self
    {
        if($origine instanceof Location) {
            $this->origine = $origine;
        } else if(!is_null($managerRegistry)){
            $this->origine = $this->findLocationByVille($managerRegistry , $origine);
        } else {
            $this->origine = $origine;
        }

//        dump($this->origine);

        return $this;
    }

    public function getDestination(ManagerRegistry $managerRegistry = null , bool $getObject = false)
    {
        if($this->origine instanceof Location) {
            return is_null($this->destination)
                ?"--"
                :$getObject
                    ?$this->destination
                    :$this->destination->getVille();
        } else if(!is_null($this->destination) and !is_null($managerRegistry)) {
            return $getObject
                ? $this->findLocationByVille($managerRegistry , $this->destination)
                : $this->findLocationByVille($managerRegistry , $this->destination)->getVille();
        } else {
            return $this->destination;
        }

    }

    public function setDestination($destination , ?ManagerRegistry $managerRegistry = null ): self
    {

        if($destination instanceof Location) {
            $this->destination = $destination;
        } else if(!is_null($managerRegistry)) {
            $this->destination = $this->findLocationByVille($managerRegistry , $destination);
        } else {
            $this->destination = $destination;
        }

//        dump($destination);

        return $this;
    }


    public function findLocationByVille(?ManagerRegistry $managerRegistry , ?string $ville ): Location{
        /**
         * @var $location Location
         */
        $locations =  $managerRegistry->getRepository(Location::class)->findBy(['ville'=>$ville]);
        if(sizeof($locations) > 0) {
            return  $locations[0];
        } else {
            $manager = $managerRegistry->getManager();
            $manager->persist(new Location($ville));
            $manager->flush();
            $locations = $managerRegistry->getRepository(Location::class)->findBy(['ville'=>$ville]) ;
            return $locations[0];
        }
    }

}
