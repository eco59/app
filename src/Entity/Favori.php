<?php

namespace App\Entity;

use App\Repository\FavoriRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FavoriRepository::class)]
class Favori
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'favoris')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Users $idPseudo = null;

    #[ORM\ManyToOne(inversedBy: 'favoris')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Event $idEvent = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isBlocked = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdPseudo(): ?Users
    {
        return $this->idPseudo;
    }

    public function setIdPseudo(?Users $idPseudo): static
    {
        $this->idPseudo = $idPseudo;

        return $this;
    }

    public function getIdEvent(): ?Event
    {
        return $this->idEvent;
    }

    public function setIdEvent(?Event $idEvent): static
    {
        $this->idEvent = $idEvent;

        return $this;
    }

    public function isBlocked(): ?bool
    {
        return $this->isBlocked;
    }

    public function setBlocked(?bool $isBlocked): static
    {
        $this->isBlocked = $isBlocked;

        return $this;
    }
}
