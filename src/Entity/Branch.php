<?php

namespace App\Entity;

use Doctrine\ORM\Mapping;
use App\Repository\BranchRepository;
use Symfony\Component\Validator\Constraints;

#[\Doctrine\ORM\Mapping\Entity(repositoryClass: BranchRepository::class)]
class Branch implements \JsonSerializable
{
    /**
     * @var string[]
     */
    public const CURRENCIES = ['EUR', 'USD'];

    /**
     * @var string[]
     */
    public const ORDER_BY = ['name', 'books'];

    #[\Doctrine\ORM\Mapping\Id]
    #[\Doctrine\ORM\Mapping\GeneratedValue]
    #[\Doctrine\ORM\Mapping\Column(type: 'integer')]
    private int $id;

    #[\Symfony\Component\Validator\Constraints\NotBlank]
    #[\Doctrine\ORM\Mapping\Column(type: 'string', length: 255)]
    private string $name = '';

    #[\Symfony\Component\Validator\Constraints\NotBlank]
    #[\Symfony\Component\Validator\Constraints\Type(type: 'float')]
    #[\Symfony\Component\Validator\Constraints\GreaterThanOrEqual(value: '0.00')]
    #[\Doctrine\ORM\Mapping\Column(type: 'decimal', precision: 5, scale: 2)]
    private float $steps = 0.00;

    #[\Symfony\Component\Validator\Constraints\Choice(choices: Branch::CURRENCIES)]
    #[\Symfony\Component\Validator\Constraints\NotBlank]
    #[\Doctrine\ORM\Mapping\Column(type: 'string', length: 3)]
    private $currency = 'EUR';

    #[\Doctrine\ORM\Mapping\Column(type: 'text', nullable: true)]
    private $ordering;

    #[\Symfony\Component\Validator\Constraints\Choice(choices: Branch::ORDER_BY)]
    #[\Symfony\Component\Validator\Constraints\NotBlank]
    #[\Doctrine\ORM\Mapping\Column(type: 'string', length: 255)]
    private $orderBy = 'name';

    #[\Doctrine\ORM\Mapping\Column(type: 'boolean')]
    private $public = false;

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'steps' => $this->getSteps(),
            'currency' => $this->getCurrency(),
            'ordering' => $this->getOrdering(),
            'orderBy' => $this->getOrderBy(),
            'public' => $this->getPublic(),
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

    public function getOrderBy(): string
    {
        return $this->orderBy;
    }

    public function setOrderBy(string $orderBy): self
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    public function getPublic(): ?bool
    {
        return $this->public;
    }

    public function setPublic(bool $public): self
    {
        $this->public = $public;

        return $this;
    }
}
