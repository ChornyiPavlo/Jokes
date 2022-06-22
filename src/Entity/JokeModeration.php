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
    private int $id;

    #[ORM\ManyToOne(targetEntity: Categories::class)]
    #[ORM\JoinColumn(name: 'categoryId', referencedColumnName: 'id', nullable: false)]
    private Categories $category;

    #[ORM\Column(type: 'string', length: 65535)]
    private string $joke;

    #[ORM\Column(type: 'string', length: 45)]
    private string $user;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $created;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategory(): Categories
    {
        return $this->category;
    }

    public function setCategory(Categories $category): self
    {
        $this->category = $category;
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

    public function setCreated(\DateTime $created): self
    {
        $this->created = $created;
        return $this;
    }

}
