<?php

namespace App\Entity;

use App\Repository\SubscriptionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SubscriptionRepository::class)
 */
class Subscription
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $plan;



    /**
     * @ORM\OneToOne(targetEntity=User::class, mappedBy="subscription", cascade={"persist", "remove"})
     */
    private $user;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=45)
     */
    private $valid_until;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    private $resolution;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    private $availability;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    private $device;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    private $support;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    private $str_price_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlan(): ?string
    {
        return $this->plan;
    }

    public function setPlan(string $plan): self
    {
        $this->plan = $plan;

        return $this;
    }



    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        // unset the owning side of the relation if necessary
        if ($user === null && $this->user !== null) {
            $this->user->setSubscription(null);
        }

        // set the owning side of the relation if necessary
        if ($user !== null && $user->getSubscription() !== $this) {
            $user->setSubscription($this);
        }

        $this->user = $user;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getValidUntil(): ?string
    {
        return $this->valid_until;
    }

    public function setValidUntil(string $valid_until): self
    {
        $this->valid_until = $valid_until;

        return $this;
    }

    public function getResolution(): ?string
    {
        return $this->resolution;
    }

    public function setResolution(?string $resolution): self
    {
        $this->resolution = $resolution;

        return $this;
    }

    public function getAvailability(): ?string
    {
        return $this->availability;
    }

    public function setAvailability(?string $availability): self
    {
        $this->availability = $availability;

        return $this;
    }

    public function getDevice(): ?string
    {
        return $this->device;
    }

    public function setDevice(?string $device): self
    {
        $this->device = $device;

        return $this;
    }

    public function getSupport(): ?string
    {
        return $this->support;
    }

    public function setSupport(?string $support): self
    {
        $this->support = $support;

        return $this;
    }

    public function getStrPriceId(): ?string
    {
        return $this->str_price_id;
    }

    public function setStrPriceId(?string $str_price_id): self
    {
        $this->str_price_id = $str_price_id;

        return $this;
    }

    public function __toString()
    {
        return $this->plan;
    }
}
