<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InvitationTokensRepository")
 */
class InvitationTokens
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
    private $emailId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $token;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Agences", inversedBy="invitationTokens")
     * @ORM\JoinColumn(nullable=false)
     */
    private $agenceId;

    public function __construct()
    {

    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getEmailId(): ?string
    {
        return $this->emailId;
    }

    public function setEmailId(string $emailId): self
    {
        $this->emailId = $emailId;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getAgenceId(): ?Agences
    {
        return $this->agenceId;
    }

    public function setAgenceId(?Agences $agenceId): self
    {
        $this->agenceId = $agenceId;

        return $this;
    }
}
