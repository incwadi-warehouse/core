<?php

namespace App\Entity;

use Doctrine\ORM\Mapping;
use App\Repository\InventoryRepository;

#[\Doctrine\ORM\Mapping\Entity(repositoryClass: InventoryRepository::class)]
class Inventory implements \JsonSerializable
{
    #[\Doctrine\ORM\Mapping\Id]
    #[\Doctrine\ORM\Mapping\GeneratedValue]
    #[\Doctrine\ORM\Mapping\Column(type: 'integer')]
    private $id;

    #[\Doctrine\ORM\Mapping\ManyToOne(targetEntity: Branch::class)]
    #[\Doctrine\ORM\Mapping\JoinColumn]
    private $branch;

    #[\Doctrine\ORM\Mapping\Column(type: 'datetime')]
    private $startedAt;

    #[\Doctrine\ORM\Mapping\Column(type: 'datetime', nullable: true)]
    private $endedAt;

    #[\Doctrine\ORM\Mapping\Column(type: 'integer')]
    private $found = 0;

    #[\Doctrine\ORM\Mapping\Column(type: 'integer')]
    private $notFound = 0;

    public function __construct()
    {
        $this->startedAt = new \DateTime();
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'branch' => $this->getBranch(),
            'startedAt' => $this->getStartedAt()->getTimestamp(),
            'endedAt' => $this->getEndedAt() !== null ? $this->getEndedAt()->getTimestamp() : null,
            'found' => $this->getFound(),
            'notFound' => $this->getNotFound(),
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBranch(): ?Branch
    {
        return $this->branch;
    }

    public function setBranch(?Branch $branch): self
    {
        $this->branch = $branch;

        return $this;
    }

    public function getStartedAt(): ?\DateTimeInterface
    {
        return $this->startedAt;
    }

    public function setStartedAt(\DateTimeInterface $startedAt): self
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function getEndedAt(): ?\DateTimeInterface
    {
        return $this->endedAt;
    }

    public function setEndedAt(?\DateTimeInterface $endedAt): self
    {
        $this->endedAt = $endedAt;

        return $this;
    }

    public function getFound(): ?int
    {
        return $this->found;
    }

    public function setFound(int $found): self
    {
        $this->found = $found;

        return $this;
    }

    public function getNotFound(): ?int
    {
        return $this->notFound;
    }

    public function setNotFound(int $notFound): self
    {
        $this->notFound = $notFound;

        return $this;
    }
}
