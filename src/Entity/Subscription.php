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
     * @ORM\Column(type="string", length=255)
     */
    private $plan;

    /**
     * @ORM\Column(type="datetime")
     */
    private $valid_until;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    private $payment_status;

    /**
     * @ORM\Column(type="boolean")
     */
    private $free_plan_used;

    /**
     * @ORM\OneToOne(targetEntity=User::class, mappedBy="subscription", cascade={"persist", "remove"})
     */
    private $user;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

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

    public function getValidUntil(): ?\DateTimeInterface
    {
        return $this->valid_until;
    }

    public function setValidUntil(\DateTimeInterface $valid_until): self
    {
        $this->valid_until = $valid_until;

        return $this;
    }

    public function getPaymentStatus(): ?string
    {
        return $this->payment_status;
    }

    public function setPaymentStatus(?string $payment_status): self
    {
        $this->payment_status = $payment_status;

        return $this;
    }

    public function getFreePlanUsed(): ?bool
    {
        return $this->free_plan_used;
    }

    public function setFreePlanUsed(bool $free_plan_used): self
    {
        $this->free_plan_used = $free_plan_used;

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
}
