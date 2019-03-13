<?php

/*
 * This script is part of baldeweg/incwadi-core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Baldeweg\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Baldeweg\Repository\CustomerRepository")
 */
class Customer implements \JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $notes;

    /**
     * @ORM\OneToMany(targetEntity="Baldeweg\Entity\Lend", mappedBy="customer")
     */
    private $lends;


    public function __construct()
    {
        $this->lends = new ArrayCollection();
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'notes' => $this->getNotes(),
            'lends' => count($this->getLends())
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * @return Collection|Lend[]
     */
    public function getLends(): Collection
    {
        return $this->lends;
    }

    public function addLend(Lend $lend): self
    {
        if (!$this->lends->contains($lend)) {
            $this->lends[] = $lend;
            $lend->setCustomer($this);
        }

        return $this;
    }

    public function removeLend(Lend $lend): self
    {
        if ($this->lends->contains($lend)) {
            $this->lends->removeElement($lend);
            if ($lend->getCustomer() === $this) {
                $lend->setCustomer(null);
            }
        }

        return $this;
    }
}
