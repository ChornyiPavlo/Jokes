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

    #[ORM\Column(type: 'integer')]
    private $category_id;

    #[ORM\Column(type: 'string', length: 65535)]
    private $joke;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategory(): ?int
    {
        return $this->category_id;
    }

    public function setCategory($category_id): self
    {
        $this->category_id = $category_id->getId();
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
