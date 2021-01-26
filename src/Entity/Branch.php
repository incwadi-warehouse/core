<?php

/*
 * This script is part of incwadi/core
 */

namespace Incwadi\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use Incwadi\Core\Repository\BranchRepository;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=BranchRepository::class)
 */
class Branch implements \JsonSerializable
{
    const CURRENCIES = ['EUR', 'USD'];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private string $name = '';

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     * @Assert\NotBlank()
     * @Assert\Type(type="float")
     * @Assert\GreaterThanOrEqual(0.00)
     */
    private float $steps = 0.00;

    /**
     * @ORM\Column(type="string", length=3)
     * @Assert\Choice(choices=Branch::CURRENCIES)
     * @Assert\NotBlank()
     */
    private $currency = 'EUR';

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $ordering;

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'steps' => $this->getSteps(),
            'currency' => $this->getCurrency(),
            'ordering' => $this->getOrdering(),
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

    public function getSteps(): ?string
    {
        return $this->steps;
    }

    public function setSteps(string $steps): self
    {
        $this->steps = $steps;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getOrdering(): ?string
    {
        return $this->ordering;
    }

    public function setOrdering(?string $ordering): self
    {
        $this->ordering = $ordering;

        return $this;
    }
}
