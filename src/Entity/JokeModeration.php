<?php

namespace App\Entity;

use App\Repository\ModerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ModerRepository::class)]
#[ORM\Table(name: '`moderation`')]
class JokeModeration
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $categoryId;

    #[ORM\Column(type: 'string', length: 65535)]
    private $joke;

    #[ORM\Column(type: 'string', length: 45)]
    private $user;

    #[ORM\Column(type: 'datetime')]
    private $created;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategoryId(): ?int
    {
        return $this->categoryId;
    }

    public function setCategoryId(int $categoryId): self
    {
        $this->categoryId = $categoryId;
        return $this;
    }

    public function getJoke(): ?string
    {
        return $this->joke;
    }

    public function setJoke(string $joke): self
    {
        $this->joke = $joke;
        return $this;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function setUser(string $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getCreated(): \DateTime
    {
        return $this->created;
    }

    public function setCreated($created): self
    {
        $this->created = $created;
        return $this;
    }

}
