<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
#[UniqueEntity(
    fields: ['email'],
    message: 'Cette adresse email existe déjà.',
)]
#[UniqueEntity(
    fields: ['username'],
    message: 'Cet utilisateur existe déjà.',
)]
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Assert\Email]
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Assert\Length(min: 2, minMessage: 'Votre nom doit contenir {limit} caractères.')]
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Assert\EqualTo(propertyPath:'confirm_password', message:'Mot de passe non identique')]
    #[Assert\Length(min: 8, minMessage: 'Votre mot de passe doit contenir {limit} caractères.')]
    private $password;

    #[Assert\EqualTo(propertyPath:'password', message:'Mot de passe non identique')]
    #[Assert\Length(min: 8, minMessage: 'Votre mot de passe doit contenir {limit} caractères.')]
    public $confirm_password;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getSalt(){}

    public function eraseCredentials(){}

    public function getUserIdentifier()
    {
        return $this->email;
    }

    public function getRoles()
    {
        return ['ROLE_ADMIN'];
    }
}
