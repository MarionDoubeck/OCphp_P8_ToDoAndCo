<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * Represents a user entity.
 *
 * This entity class defines the properties and behaviors of a user in the application.
 * It is mapped to the database table corresponding to the User entity.
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $username = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 60)]
    private ?string $email = null;

    #[ORM\OneToMany(targetEntity: Task::class, mappedBy: 'author')]
    private Collection $tasks;


    public function __construct()
    {
        $this->tasks = new ArrayCollection();

    }//end _construct()


    /**
     * Gets the ID of the user.
     *
     * @return int|null The ID of the user.
     */
    public function getId(): ?int
    {
        return $this->id;

    }//end getId()


    /**
     * Gets the username of the user.
     *
     * @return string|null The username of the user.
     */
    public function getUsername(): ?string
    {
        return $this->username;

    }//end getUsername()


    /**
     * Sets the username of the user.
     *
     * @param string $username The username of the user.
     *
     * @return static The updated User entity.
     */
    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;

    }//end setUsername()


    /**
     * Returns a visual identifier that represents this user.
     *
     * @return string The user identifier.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;

    }//end getUserIdentifier()


    /**
     * Gets the roles of the user.
     *
     * @return array The roles of the user.
     *
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);

    }//end getRoles()


    /**
     * Sets the roles of the user.
     *
     * @param array $roles The roles of the user.
     *
     * @return static The updated User entity.
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;

    }//end setRoles()


    /**
     * Gets the hashed password of the user.
     *
     * @return string The hashed password of the user.
     */
    public function getPassword(): string
    {
        return $this->password;

    }//end getPassword()


    /**
     * Sets the hashed password of the user.
     *
     * @param string $password The hashed password of the user.
     *
     * @return static The updated User entity.
     */
    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;

    }//end setPassword()


    /**
     * Erases the user's credentials.
     *
     * This method should clear any sensitive data stored temporarily on the user.
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here.
        // $this->plainPassword = null;
    }//end eraseCredentials()


    /**
     * Gets the email of the user.
     *
     * @return string|null The email of the user.
     */
    public function getEmail(): ?string
    {
        return $this->email;

    }//end getEmail()


    /**
     * Sets the email of the user.
     *
     * @param string $email The email of the user.
     *
     * @return static The updated User entity.
     */
    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;

    }//end setEmail()


    /**
     * Gets the tasks associated with the user.
     *
     * @return Collection<int, Task> The tasks associated with the user.
     */
    public function getTasks(): Collection
    {
        return $this->tasks;

    }//end getTasks()


}
