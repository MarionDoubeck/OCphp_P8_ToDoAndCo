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
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?bool $isDone = null;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    private ?User $author = null;


    /**
     * Gets the ID of the task.
     *
     * @return int|null The ID of the task.
     */
    public function getId(): ?int
    {
        return $this->id;
    }


    /**
     * Gets the title of the task.
     *
     * @return string|null The title of the task.
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }


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
    }


    /**
     * Gets the content of the task.
     *
     * @return string|null The content of the task.
     */
    public function getContent(): ?string
    {
        return $this->content;
    }


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
    }


    /**
     * Gets the creation date of the task.
     *
     * @return \DateTimeImmutable|null The creation date of the task.
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }


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
    }


    /**
     * Checks if the task is marked as done.
     *
     * @return bool|null True if the task is marked as done, false otherwise.
     */
    public function isIsDone(): ?bool
    {
        return $this->isDone;
    }


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
    }


    /**
     * Toggles the status of the task (done/undone).
     *
     * @param mixed $flag The flag indicating whether the task is done or not.
     */
    public function toggle($flag)
    {
        $this->isDone = $flag;
    }


    /**
     * Gets the author of the task.
     *
     * @return User|null The author of the task.
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }


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
    }

}
