<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le titre ne peut pas être vide')]
    #[Assert\Length(max: 255, maxMessage: 'Le titre ne peut pas dépasser {{ limit }} caractères')]
    private ?string $Titre = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'La description ne peut pas être vide')]
    private ?string $Description = null;

    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\NotBlank(message: 'Le nombre de joueurs ne peut pas être vide')]
    #[Assert\Positive(message: 'Le nombre de joueurs doit être positif')]
    private ?int $Nombre_de_joueur = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: 'La date de début ne peut pas être vide')]
    private ?\DateTimeInterface $date_heure_debut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: 'La date de fin ne peut pas être vide')]
    #[Assert\GreaterThan(propertyPath: 'date_heure_debut', message: 'La date de fin doit être après la date de début')]
    private ?\DateTimeInterface $date_heure_fin = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Users $pseudo = null;

    /**
     * @var Collection<int, Favori>
     */
    #[ORM\OneToMany(targetEntity: Favori::class, mappedBy: 'idEvent', orphanRemoval: true)]
    private Collection $favoris;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $Image = null;

    #[ORM\Column(length: 20, options: ['default' => 'pending'])]
    private ?string $status = 'pending';

    public function __construct()
    {
        $this->favoris = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->Titre;
    }

    public function setTitre(string $Titre): static
    {
        $this->Titre = $Titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): static
    {
        $this->Description = $Description;

        return $this;
    }

    public function getNombreDeJoueur(): ?int
    {
        return $this->Nombre_de_joueur;
    }

    public function setNombreDeJoueur(int $Nombre_de_joueur): static
    {
        $this->Nombre_de_joueur = $Nombre_de_joueur;

        return $this;
    }

    public function getDateHeureDebut(): ?\DateTimeInterface
    {
        return $this->date_heure_debut;
    }

    public function setDateHeureDebut(\DateTimeInterface $date_heure_debut): static
    {
        $this->date_heure_debut = $date_heure_debut;

        return $this;
    }

    public function getDateHeureFin(): ?\DateTimeInterface
    {
        return $this->date_heure_fin;
    }

    public function setDateHeureFin(\DateTimeInterface $date_heure_fin): static
    {
        $this->date_heure_fin = $date_heure_fin;

        return $this;
    }

    public function getPseudo(): ?Users
    {
        return $this->pseudo;
    }

    public function setPseudo(?Users $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    /**
     * @return Collection<int, Favori>
     */
    public function getFavoris(): Collection
    {
        return $this->favoris;
    }

    public function addFavori(Favori $favori): static
    {
        if (!$this->favoris->contains($favori)) {
            $this->favoris->add($favori);
            $favori->setIdEvent($this);
        }

        return $this;
    }

    public function removeFavori(Favori $favori): static
    {
        if ($this->favoris->removeElement($favori)) {
            // set the owning side to null (unless already changed)
            if ($favori->getIdEvent() === $this) {
                $favori->setIdEvent(null);
            }
        }

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->Image;
    }

    public function setImage(?string $Image): static
    {
        $this->Image = $Image;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }
}
