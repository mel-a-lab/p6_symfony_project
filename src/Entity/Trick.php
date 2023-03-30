<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\TrickRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


#[ORM\Entity(repositoryClass: TrickRepository::class)]
#[UniqueEntity('name', message: 'Ce nom est déjà utilisé')]
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

    #[ORM\ManyToOne(targetEntity: Group::class)]
    private Group $group;

    #[ORM\Column(length: 255)]
    private ?string $slug = 'test';

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'proper_trick', targetEntity: Comment::class)]
    private Collection $comments;


    public function __construct()
    {
        $this->tricksImages = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateSlug(): void
    {
        $slugger = new AsciiSlugger();
        $this->slug = $slugger->slug($this->name);
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


    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getGroup(): ?Group
    {
        return $this->group;
    }

    public function setGroup(Group $group): self
    {
        $this->group = $group;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setProperTrick($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getProperTrick() === $this) {
                $comment->setProperTrick(null);
            }
        }

        return $this;
    }
}
