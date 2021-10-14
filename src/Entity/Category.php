<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=45, unique=true)
     * @Assert\NotBlank(message = "Category name is required")
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Video::class, mappedBy="category")
     */
    private $path;

    public function __construct()
    {
        $this->path = new ArrayCollection();
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
     * @return Collection|Video[]
     */
    public function getPath(): Collection
    {
        return $this->path;
    }

    public function addPath(Video $path): self
    {
        if (!$this->path->contains($path)) {
            $this->path[] = $path;
            $path->setCategory($this);
        }

        return $this;
    }

    public function removePath(Video $path): self
    {
        if ($this->path->removeElement($path)) {
            // set the owning side to null (unless already changed)
            if ($path->getCategory() === $this) {
                $path->setCategory(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}