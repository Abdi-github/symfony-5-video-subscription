<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Comment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message = "Content is  required")
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     * 
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Video::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $video;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    private $updated_at;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="liked_comments")
     * @ORM\JoinTable(name="user_likedComments")
     */
    private $usersLikeComments;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="disliked_comments")
     * @ORM\JoinTable(name="user_dislikedComments")
     */
    private $usersDislikeComments;

    public function __construct()
    {
        $this->usersLikeComments = new ArrayCollection();
        $this->usersDislikeComments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getVideo(): ?Video
    {
        return $this->video;
    }

    public function setVideo(?Video $video): self
    {
        $this->video = $video;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAt($created_at): self
    {
        if (isset($this->created_at2))
            $this->created_at = $this->created_at2;
        else
            $this->created_at = new \DateTime();
        return $this;
    }

    public function setCreatedAtForFixtures($created_at): self
    {
        $this->created_at2 = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsersLikeComments(): Collection
    {
        return $this->usersLikeComments;
    }

    public function addUsersLikeComments(User $user): self
    {
        if (!$this->usersLikeComments->contains($user)) {
            $this->usersLikeComments[] = $user;
            $user->addLikedComment($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->usersLikeComments->removeElement($user)) {
            $user->removeLikedComment($this);
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsersDislikeComments(): Collection
    {
        return $this->usersDislikeComments;
    }

    public function addUsersDislikeComment(User $usersDislikeComment): self
    {
        if (!$this->usersDislikeComments->contains($usersDislikeComment)) {
            $this->usersDislikeComments[] = $usersDislikeComment;
            $usersDislikeComment->addDislikedComment($this);
        }

        return $this;
    }

    public function removeUsersDislikeComment(User $usersDislikeComment): self
    {
        if ($this->usersDislikeComments->removeElement($usersDislikeComment)) {
            $usersDislikeComment->removeDislikedComment($this);
        }

        return $this;
    }
}
