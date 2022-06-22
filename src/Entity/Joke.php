<?php

namespace App\Entity;

use App\Repository\JokeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JokeRepository::class)]
#[ORM\Table(name: '`joke`')]
class Joke
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Categories::class)]
    #[ORM\JoinColumn(name: 'categoryId', referencedColumnName: 'id', nullable: false)]
    private Categories $category;

    #[ORM\Column(type: 'string', length: 65535)]
    private $joke;

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

}
