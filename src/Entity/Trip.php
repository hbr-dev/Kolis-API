<?php

namespace App\Entity;

use App\Repository\TripRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use SSH\MyJwtBundle\Entity\AbstractEntity;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: TripRepository::class)]
class Trip extends AbstractEntity
{

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"IDENTITY")]
    #[ORM\Column(name:"id", type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type: 'uuid')]
    private ?Uuid $code = null;

    #[ORM\Column(length: 300)]
    private ?string $pickUPLocation = null;

    #[ORM\Column(length: 300)]
    private ?string $deliveryLocation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;
    
    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column]
    private ?\DateTime $createdAt = null;

    #[ORM\Column]
    private ?\DateTime $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'trips')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Transporter $transporter = null;

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

    public function getPickUPLocation(): ?string
    {
        return $this->pickUPLocation;
    }

    public function setPickUPLocation(string $pickUPLocation): static
    {
        $this->pickUPLocation = $pickUPLocation;

        return $this;
    }

    public function getDeliveryLocation(): ?string
    {
        return $this->deliveryLocation;
    }

    public function setDeliveryLocation(string $deliveryLocation): static
    {
        $this->deliveryLocation = $deliveryLocation;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

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

    public function getTransporter(): ?Transporter
    {
        return $this->transporter;
    }

    public function setTransporter(?Transporter $transporter): static
    {
        $this->transporter = $transporter;

        return $this;
    }
}
