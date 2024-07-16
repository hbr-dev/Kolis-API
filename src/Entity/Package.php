<?php

namespace App\Entity;

use App\Repository\PackageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use SSH\MyJwtBundle\Entity\AbstractEntity;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: PackageRepository::class)]
class Package extends AbstractEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"IDENTITY")]
    #[ORM\Column(name:"id", type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type: 'uuid')]
    private ?Uuid $code = null;

    #[ORM\Column(length: 300, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $weight = null;

    /**
     * @var list<float>
     */
    #[ORM\Column]
    private array $dimensions = [];

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 3, nullable: true)]
    private ?string $transportationCharge = null;
    
    #[ORM\Column(type:"string", length:255)]
    private ?string $status = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $img = null;

    #[ORM\Column]
    private ?\DateTime $createdAt = null;

    #[ORM\Column]
    private ?\DateTime $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'packagesAsSender')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $sender = null;

    #[ORM\ManyToOne(inversedBy: 'packages')]
    private ?Trip $trip = null;

    #[ORM\Column(length: 255)]
    private ?string $receiverFirstName = null;

    #[ORM\Column(length: 255)]
    private ?string $receiverLastName = null;

    #[ORM\Column(length: 12)]
    private ?string $receiverPhoneNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $receiverEmail = null;

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

    public function getDescription(): ?string
    {
        return $this->description;  
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(float $weight): static
    {
        $this->weight = $weight;

        return $this;
    }

    public function getDimensions(): array
    {
        return $this->dimensions;
    }

    public function setDimensions(array $dimensions): static
    {
        $this->dimensions = $dimensions;

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

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(?string $img): static
    {
        $this->img = $img;

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

    public function getTransportationCharge(): ?string
    {
        return $this->transportationCharge;
    }

    public function setTransportationCharge(?string $transportationCharge): static
    {
        $this->transportationCharge = $transportationCharge;

        return $this;
    }

    public function getSender(): ?Client
    {
        return $this->sender;
    }

    public function setSender(?Client $sender): static
    {
        $this->sender = $sender;

        return $this;
    }

    public function getTrip(): ?Trip
    {
        return $this->trip;
    }

    public function setTrip(?Trip $trip): static
    {
        $this->trip = $trip;

        return $this;
    }

    public function getReceiverFirstName(): ?string
    {
        return $this->receiverFirstName;
    }

    public function setReceiverFirstName(string $receiverFirstName): static
    {
        $this->receiverFirstName = $receiverFirstName;

        return $this;
    }

    public function getReceiverLastName(): ?string
    {
        return $this->receiverLastName;
    }

    public function setReceiverLastName(string $receiverLastName): static
    {
        $this->receiverLastName = $receiverLastName;

        return $this;
    }

    public function getReceiverPhoneNumber(): ?string
    {
        return $this->receiverPhoneNumber;
    }

    public function setReceiverPhoneNumber(string $receiverPhoneNumber): static
    {
        $this->receiverPhoneNumber = $receiverPhoneNumber;

        return $this;
    }

    public function getReceiverEmail(): ?string
    {
        return $this->receiverEmail;
    }

    public function setReceiverEmail(?string $receiverEmail): static
    {
        $this->receiverEmail = $receiverEmail;

        return $this;
    }
}
