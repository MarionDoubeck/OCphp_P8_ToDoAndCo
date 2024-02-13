<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a task entity.
 *
 * This entity class defines the properties and behaviors of a task in the application.
 * It is mapped to the database table corresponding to the Task entity.
 */
#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $title = null; // Title of the task.

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null; // Content of the task.

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null; // Creation date of the task.

    #[ORM\Column]
    private ?bool $isDone = null; // Indicates if the task is done or not.

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    private ?User $author = null; // Author of the task.


    /**
     * Gets the ID of the task.
     *
     * @return int|null The ID of the task.
     */
    public function getId(): ?int
    {
        return $this->id;

    }//end getId()


    /**
     * Gets the title of the task.
     *
     * @return string|null The title of the task.
     */
    public function getTitle(): ?string
    {
        return $this->title;

    }//end getTitle()


    /**
     * Sets the title of the task.
     *
     * @param string $title The title of the task.
     *
     * @return static The updated Task entity.
     */
    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;

    }//end setTitle()


    /**
     * Gets the content of the task.
     *
     * @return string|null The content of the task.
     */
    public function getContent(): ?string
    {
        return $this->content;

    }//end getContent()


    /**
     * Sets the content of the task.
     *
     * @param string $content The content of the task.
     *
     * @return static The updated Task entity.
     */
    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;

    }//end setContent()


    /**
     * Gets the creation date of the task.
     *
     * @return \DateTimeImmutable|null The creation date of the task.
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;

    }//end getCreatedAt()


    /**
     * Sets the creation date of the task.
     *
     * @param \DateTimeImmutable $createdAt The creation date of the task.
     *
     * @return static The updated Task entity.
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;

    }//end setCreatedAt()


    /**
     * Checks if the task is marked as done.
     *
     * @return bool|null True if the task is marked as done, false otherwise.
     */
    public function isIsDone(): ?bool
    {
        return $this->isDone;

    }//end isIsDone()


    /**
     * Sets the status of the task (done/undone).
     *
     * @param bool $isDone The status of the task.
     *
     * @return static The updated Task entity.
     */
    public function setIsDone(bool $isDone): static
    {
        $this->isDone = $isDone;

        return $this;

    }//end setIsDone()


    /**
     * Toggles the status of the task (done/undone).
     *
     * @param mixed $flag The flag indicating whether the task is done or not.
     * 
     * @return void
     */
    public function toggle($flag)
    {
        $this->isDone = $flag;

    }//end toggle()


    /**
     * Gets the author of the task.
     *
     * @return User|null The author of the task.
     */
    public function getAuthor(): ?User
    {
        return $this->author;

    }//end getAuthor()


    /**
     * Sets the author of the task.
     *
     * @param User|null $author The author of the task.
     *
     * @return static The updated Task entity.
     */
    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;

    }//end setAuthor()


}//end class
