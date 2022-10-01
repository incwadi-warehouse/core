<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use App\Repository\BranchRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Validator\Constraints as Assert;

#[Entity(repositoryClass: BranchRepository::class)]
class Branch implements \JsonSerializable
{
    /**
     * @var string[]
     */
    public final const CURRENCIES = ['EUR', 'USD'];

    /**
     * @var string[]
     */
    public final const ORDER_BY = ['name', 'books'];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[Assert\NotBlank]
    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $name = '';

    #[Assert\NotBlank]
    #[Assert\Type(type: 'float')]
    #[Assert\GreaterThanOrEqual(value: '0.00')]
    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private float $steps = 0.00;

    #[Assert\Choice(choices: Branch::CURRENCIES)]
    #[Assert\NotBlank]
    #[ORM\Column(type: Types::STRING, length: 3)]
    private ?string $currency = 'EUR';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $ordering = null;

    // @deprecated
    #[Assert\Choice(choices: Branch::ORDER_BY)]
    #[Assert\NotBlank]
    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $orderBy = 'name';

    #[ORM\Column(type: Types::BOOLEAN)]
    private $public = false;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $pricelist = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private $cart = false;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'steps' => $this->getSteps(),
            'currency' => $this->getCurrency(),
            'ordering' => $this->getOrdering(),
            'orderBy' => $this->getOrderBy(),
            'public' => $this->getPublic(),
            'pricelist' => $this->getPricelist(),
            'cart' => $this->getCart(),
            'content' => $this->getContent()
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

    // @deprecated
    public function getOrderBy(): string
    {
        return $this->orderBy;
    }

    // @deprecated
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

    public function getPricelist(): ?string
    {
        return $this->pricelist;
    }

    public function setPricelist(string $pricelist): self
    {
        $this->pricelist = $pricelist;

        return $this;
    }

    public function getCart(): ?bool
    {
        return $this->cart;
    }

    public function setCart(bool $cart): self
    {
        $this->cart = $cart;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }
}
