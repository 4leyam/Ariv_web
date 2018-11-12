<?php

namespace App\Entity;

use App\Controller\AppConstants;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NzelaUserRepository")
 * @UniqueEntity(
 *     fields={"emaiId"},
 *     message="Un compte est deja associe a cette adresse"
 * )
 * @UniqueEntity(
 *     fields={"username" , "prenom"},
 *     message="Nom et prenom deja Associe a un compte"
 * )
 * @UniqueEntity(
 *     fields={"pseudo"},
 *     message="Pseudo deja Utilise"
 * )

 */
class NzelaUser implements UserInterface , \Serializable {
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $pseudo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $emaiId;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active = false;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;

    /**
     * @ORM\Column(type="integer")
     */
    private $role;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $telephone;



    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @Assert\EqualTo(propertyPath = "password" , message = "les deux mot de passe doivent etre Identique")
     */
    public $passConfirm;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Agences", inversedBy="nzelaUsers")
     */
    private $idAgence;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmaiId(): ?string
    {
        return $this->emaiId;
    }

    public function setEmaiId(string $emaiId): self
    {
        $this->emaiId = $emaiId;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getRole(): array
    {
        $tmp = [];
        for($i = 0 ; $i<= $this->role ; $i++ ) {
         $tmp[] = AppConstants::ROLES[$i];
        }
        return $tmp;
    }

    public function setRole(int $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }


    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles() {
        // TODO: Implement getRoles() method.
        $role_level = $this->role;
        return [AppConstants::ROLES[$role_level]];
    }
    public function getSalt() {
        // TODO: Implement getSalt() method.
    }

    /**
     * @return mixed
     */
    public function getPseudo() {
        return $this->pseudo;
    }

    /**
     * @param mixed $pseudo
     */
    public function setPseudo($pseudo): void {
        $this->pseudo = $pseudo;
    }

    /**
     * @return mixed
     */
    public function getPassConfirm() {
        return $this->passConfirm;
    }
    public function eraseCredentials() {
        // TODO: Implement eraseCredentials() method.
    }

    public function serialize() {
        // TODO: Implement serialize() method.
        return serialize([
            $this->username ,
            $this->prenom ,
            $this->telephone ,
            $this->role ,
            $this->password ,
            $this->idAgence ,
            $this->id
                  ]);
    }
    public function unserialize($serialized) {
        // TODO: Implement unserialize() method.

        //
        list(
            $this->username ,
            $this->prenom ,
            $this->telephone ,
            $this->role ,
            $this->password ,
            $this->idAgence ,
            $this->id
            ) = unserialize($serialized);
    }

    public function getIdAgence(): ?Agences
    {
        return $this->idAgence;
    }

    public function setIdAgence(?Agences $idAgence): self
    {
        $this->idAgence = $idAgence;

        return $this;
    }
}
