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

    #[ORM\Column]
    private ?\DateTime $createdAt = null;

    #[ORM\Column]
    private ?\DateTime $expiration_date = null;

    #[ORM\Column]
    private ?\DateTime $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'packs')]
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



    public function getExpirationDate(): ?\DateTime
    {
        return $this->expiration_date;
    }



    public function setExpirationDate(\DateTime $expiration_date): static
    {
        $this->expiration_date = $expiration_date;

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
