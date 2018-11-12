<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LocationRepository")
 */
class Location
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ville;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pays;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Departs", mappedBy="origine", orphanRemoval=true)
     */
    private $departs;

    public function __construct(string $ville = null , string $pays = null)
    {
        $this->ville = $ville;
        $this->pays = $pays;
        $this->departs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(?string $pays): self
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * @return Collection|Departs[]
     */
    public function getDeparts(): Collection
    {
        return $this->departs;
    }

    public function addDepart(Departs $depart): self
    {
        if (!$this->departs->contains($depart)) {
            $this->departs[] = $depart;
            $depart->setOrigine($this);
        }

        return $this;
    }

    public function removeDepart(Departs $depart): self
    {
        if ($this->departs->contains($depart)) {
            $this->departs->removeElement($depart);
            // set the owning side to null (unless already changed)
            if ($depart->getOrigine() === $this) {
                $depart->setOrigine(null);
            }
        }

        return $this;
    }
}
