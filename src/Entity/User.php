<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\UserRepository;
use App\State\UserPasswordHasher;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
#[ApiResource(paginationItemsPerPage: 10)]
#[Get(security:'is_granted("ROLE_USER","ROLE_ADMIN")')]
#[GetCollection(security:'is_granted("ROLE_USER","ROLE_ADMIN")')]
#[Delete(security:'is_granted("ROLE_ADMIN")')]
#[ApiResource(
    uriTemplate: "/register",
    operations: [
        new Post(processor: UserPasswordHasher::class),
        new Put(processor: UserPasswordHasher::class,
                uriTemplate: "/update/{id}",
                denormalizationContext: ['groups' => ['update']],),
    ],
    denormalizationContext: ['groups' => ['register']],
    security:'is_granted("ROLE_ADMIN")',
)]
#[UniqueEntity(fields: ["username"])]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(["register","update"])]
    #[ORM\Column(length: 180, unique: true)]
    private ?string $username = null;

    #[Groups(["register", "update"])]
    #[ORM\Column(length: 180)]
    private ?string $prenom = null;

    #[Groups(["register","update"])]
    #[ORM\Column(length: 180)]
    private ?string $nom = null;

    #[Groups("update")]
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[Groups(["register","update"])]
    #[ORM\Column]
    private ?string $password = null;

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): void
    {
        $this->prenom = $prenom;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): void
    {
        $this->nom = $nom;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
