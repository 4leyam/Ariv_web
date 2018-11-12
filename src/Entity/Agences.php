<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass="App\Repository\AgencesRepository")
 */
class Agences
{
    /**
     * @Groups("self")
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups("self")
     * @ORM\Column(type="string", length=255)
     */
    private $nomAgence;

    /**
     * @Groups("self_more")
     * @ORM\Column(type="string", length=255)
     */
    private $adresseAgence;

    /**
     * @Groups("self_more")
     * @ORM\Column(type="string", length=255)
     */
    private $contactAgence;

    /**
     * @Groups("self_more")
     * @ORM\Column(type="string", length=255)
     */
    private $emailAgence;

    /**
     * @Groups("self_more")
     * @ORM\Column(type="text")
     */
    private $plusInfo;

    /**
     * @Groups("self")
     * @ORM\Column(type="integer", nullable=true)
     */
    private $avis;

    /**
     * @Groups("self")
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\File(
     *     maxSize = "1024k",
     *     mimeTypes = {"image/jpeg", "image/png"},
     *     mimeTypesMessage = "Entrez une image au bon format s'il vous plait"
     * )
     */
    private $agenceLogo;



    /**
     * @Groups("self_rate")
     * @ORM\OneToMany(targetEntity="App\Entity\Commentaires", mappedBy="agence", orphanRemoval=true)
     */
    private $comments;

    /**
     * @Groups("self_more")
     * @ORM\OneToMany(targetEntity="App\Entity\Departs", mappedBy="agence", orphanRemoval=true)
     */
    private $departs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\InvitationTokens", mappedBy="agenceId", orphanRemoval=true)
     */
    private $invitationTokens;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\NzelaUser", mappedBy="idAgence")
     */
    private $nzelaUsers;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->departs = new ArrayCollection();
        $this->invitationTokens = new ArrayCollection();
        $this->nzelaUsers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id) {
        $this->id = $id;
    }


    public function getNomAgence(): ?string
    {
        return $this->nomAgence;
    }

    public function setNomAgence(string $nomAgence): self
    {
        $this->nomAgence = $nomAgence;

        return $this;
    }

    public function getAdresseAgence(): ?string
    {
        return $this->adresseAgence;
    }

    public function setAdresseAgence(string $adresseAgence): self
    {
        $this->adresseAgence = $adresseAgence;

        return $this;
    }

    public function getContactAgence(): ?string
    {
        return $this->contactAgence;
    }

    public function setContactAgence(string $contactAgence): self
    {
        $this->contactAgence = $contactAgence;

        return $this;
    }

    public function getEmailAgence(): ?string
    {
        return $this->emailAgence;
    }

    public function setEmailAgence(string $emailAgence): self
    {
        $this->emailAgence = $emailAgence;

        return $this;
    }

    public function getPlusInfo(): ?string
    {
        return $this->plusInfo;
    }

    public function setPlusInfo(string $plusInfo): self
    {
        $this->plusInfo = $plusInfo;

        return $this;
    }

    public function getAvis(): ?int
    {
        return $this->avis;
    }

    public function setAvis(?int $avis): self
    {
        $this->avis = $avis;

        return $this;
    }

    public function getAgenceLogo()
    {
        return $this->agenceLogo;
    }

    public function setAgenceLogo( $agenceLogo): self
    {
        $this->agenceLogo = $agenceLogo;

        return $this;
    }


    /**
     * @return Collection|Commentaires[]
     */
    public function getComments(): ?Collection
    {
        return $this->comments;
    }

    /**
     * @param mixed $comments
     */
    public function setComments($comments): void
    {
        $this->comments = $comments;
    }

    public function addComment(Commentaires $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setAgence($this);
        }

        return $this;
    }

    public function removeComment(Commentaires $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getAgence() === $this) {
                $comment->setAgence(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Departs[]
     */
    public function getDeparts(): ?Collection
    {
        return $this->departs;
    }

    public function addDepart(Departs $depart): self
    {
        if (!$this->departs->contains($depart)) {
            $this->departs[] = $depart;
            $depart->setAgence($this);
        }

        return $this;
    }

    public function removeDepart(Departs $depart): self
    {
        if ($this->departs->contains($depart)) {
            $this->departs->removeElement($depart);
            // set the owning side to null (unless already changed)
            if ($depart->getAgence() === $this) {
                $depart->setAgence(null);
            }
        }

        return $this;
    }

    /**
     * @param mixed $departs
     */
    public function setDeparts($departs): void
    {
        $this->departs = $departs;
    }

    /**
     * @param mixed $nzelaUsers
     */
    public function setNzelaUsers($nzelaUsers): void
    {
        $this->nzelaUsers = $nzelaUsers;
    }

    /**
     * @param mixed $invitationTokens
     */
    public function setInvitationTokens($invitationTokens): void
    {
        $this->invitationTokens = $invitationTokens;
    }

    /**
     * @return Collection|InvitationTokens[]
     */
    public function getInvitationTokens(): ?Collection
    {
        return $this->invitationTokens;
    }

    public function addInvitationToken(InvitationTokens $invitationToken): self
    {
        if (!$this->invitationTokens->contains($invitationToken)) {
            $this->invitationTokens[] = $invitationToken;
            $invitationToken->setAgenceId($this);
        }

        return $this;
    }

    public function removeInvitationToken(InvitationTokens $invitationToken): self
    {
        if ($this->invitationTokens->contains($invitationToken)) {
            $this->invitationTokens->removeElement($invitationToken);
            // set the owning side to null (unless already changed)
            if ($invitationToken->getAgenceId() === $this) {
                $invitationToken->setAgenceId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|NzelaUser[]
     */
    public function getNzelaUsers(): ?Collection
    {
        return $this->nzelaUsers;
    }

    public function addNzelaUser(NzelaUser $nzelaUser): self
    {
        if (!$this->nzelaUsers->contains($nzelaUser)) {
            $this->nzelaUsers[] = $nzelaUser;
            $nzelaUser->setIdAgence($this);
        }

        return $this;
    }

    public function removeNzelaUser(NzelaUser $nzelaUser): self
    {
        if ($this->nzelaUsers->contains($nzelaUser)) {
            $this->nzelaUsers->removeElement($nzelaUser);
            // set the owning side to null (unless already changed)
            if ($nzelaUser->getIdAgence() === $this) {
                $nzelaUser->setIdAgence(null);
            }
        }

        return $this;
    }
}
