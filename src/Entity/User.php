<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity("email")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message = "Password should be entered!")
     * @Assert\Length(min=6, minMessage = "password should be atleast 6 characters")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=45)
     * @Assert\NotBlank(message = "First name is required.")
     */
    private $first_name;

    /**
     * @ORM\Column(type="string", length=45)
     * @Assert\NotBlank(message = "Last name is required.")
     */
    private $last_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $vimeo_api_key;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="user")  
     */
    private $comments;

    /**
     * @ORM\ManyToMany(targetEntity=Comment::class, inversedBy="usersLikeComments")
     * @ORM\JoinTable(name="user_likedComments")
     */
    private $liked_comments;

    /**
     * @ORM\ManyToMany(targetEntity=Comment::class, inversedBy="usersDislikeComments")
     * @ORM\JoinTable(name="user_dislikedComments")
     */
    private $disliked_comments;

    /**
     * @ORM\OneToOne(targetEntity=Subscription::class, inversedBy="user", cascade={"persist", "remove"})
     */
    private $subscription;

    public function __construct()
    {
        $this->liked_comments = new ArrayCollection();
        $this->disliked_comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): self
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getVimeoApiKey(): ?string
    {
        return $this->vimeo_api_key;
    }

    public function setVimeoApiKey(?string $vimeo_api_key): self
    {
        $this->vimeo_api_key = $vimeo_api_key;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getLikedComments(): Collection
    {
        return $this->liked_comments;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addLikedComment(Comment $likedComment): self
    {
        if (!$this->liked_comments->contains($likedComment)) {
            $this->liked_comments[] = $likedComment;
        }

        return $this;
    }

    public function removeLikedComment(Comment $likedComment): self
    {
        $this->liked_comments->removeElement($likedComment);

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getDislikedComments(): Collection
    {
        return $this->disliked_comments;
    }

    public function addDislikedComment(Comment $dislikedComment): self
    {
        if (!$this->disliked_comments->contains($dislikedComment)) {
            $this->disliked_comments[] = $dislikedComment;
        }

        return $this;
    }

    public function removeDislikedComment(Comment $dislikedComment): self
    {
        $this->disliked_comments->removeElement($dislikedComment);

        return $this;
    }

    public function getSubscription(): ?Subscription
    {
        return $this->subscription;
    }

    public function setSubscription(?Subscription $subscription): self
    {
        $this->subscription = $subscription;

        return $this;
    }
}
