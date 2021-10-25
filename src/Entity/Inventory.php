<?php

namespace App\Entity;

use App\Repository\InventoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;

#[Entity(repositoryClass: InventoryRepository::class)]
class Inventory implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Branch::class)]
    #[ORM\JoinColumn]
    private $branch;

    #[ORM\Column(type: 'datetime')]
    private $startedAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $endedAt;

    #[ORM\Column(type: 'integer')]
    private $found = 0;

    #[ORM\Column(type: 'integer')]
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
            'endedAt' => null !== $this->getEndedAt() ? $this->getEndedAt()->getTimestamp() : null,
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
