<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\TrickRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


#[ORM\Entity(repositoryClass: TrickRepository::class)]
#[UniqueEntity('name')]
#[ORM\HasLifecycleCallbacks]
class Trick
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'trick', targetEntity: TrickImage::class)]
    private Collection $tricksImages;

    #[ORM\OneToMany(mappedBy: 'trick', targetEntity: Comment::class)]
    private Collection $comments;

    #[ORM\ManyToOne(targetEntity: Group::class)]
    private Collection $group;

    #[ORM\Column(length: 255)]
    private ?string $slug = 'test';


    public function __construct()
    {
        $this->tricksImages = new ArrayCollection();
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateSlug(): void
    {
        //récupérer name, remplacer les espaces par tirets, regarder les fonctions php
        $slugify = new Slugify();
        $this->slug = $slugify->slugify($this->name);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }


    /**
     * @return Collection<int, TrickImage>
     */
    public function getTricksImages(): Collection
    {
        return $this->tricksImages;
    }

    public function addTricksImage(TrickImage $tricksImage): self
    {
        if (!$this->tricksImages->contains($tricksImage)) {
            $this->tricksImages->add($tricksImage);
            $tricksImage->setTrick($this);
        }

        return $this;
    }

    public function removeTricksImage(TrickImage $tricksImage): self
    {
        if ($this->tricksImages->removeElement($tricksImage)) {
            // set the owning side to null (unless already changed)
            if ($tricksImage->getTrick() === $this) {
                $tricksImage->setTrick(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    // getter et setter comment comme tricksimages
}
