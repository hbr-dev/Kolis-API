<?php

namespace App\Entity;

use App\Repository\PackRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use SSH\MyJwtBundle\Entity\AbstractEntity;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: PackRepository::class)]
class Pack extends AbstractEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"IDENTITY")]
    #[ORM\Column(name:"id", type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type: 'uuid')]
    private ?Uuid $code = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 3)]
    private ?string $price = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $freeTrialPeriod = null;

    #[ORM\Column]
    private ?int $subscriptionPeriod = null;

    #[ORM\Column]
    private ?\DateTime $createdAt = null;

    #[ORM\Column]
    private ?\DateTime $updatedAt = null;


    public function __construct($data = [])
    {
        parent::__construct($data);
    }


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getCode(): ?Uuid
    {
        return $this->code;
    }



    public function setCode(Uuid $code): static
    {
        $this->code = $code;

        return $this;
    }



    public function getPrice(): ?string
    {
        return $this->price;
    }



    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }



    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }



    public function setCreatedAt(\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }



    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }


    
    public function setUpdatedAt(\DateTime $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getFreeTrialPeriod(): ?int
    {
        return $this->freeTrialPeriod;
    }

    public function setFreeTrialPeriod(int $freeTrialPeriod): static
    {
        $this->freeTrialPeriod = $freeTrialPeriod;

        return $this;
    }

    public function getSubscriptionPeriod(): ?int
    {
        return $this->subscriptionPeriod;
    }

    public function setSubscriptionPeriod(int $subscriptionPeriod): static
    {
        $this->subscriptionPeriod = $subscriptionPeriod;

        return $this;
    }
}
