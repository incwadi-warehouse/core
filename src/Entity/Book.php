<?php

/*
 * This script is part of incwadi/core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Incwadi\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Incwadi\Core\Repository\BookRepository")
 */
class Book implements \JsonSerializable
{
    /**
     * @var string[]
     */
    const TYPES = ['paperback', 'hardcover'];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Incwadi\Core\Entity\Branch")
     */
    private $branch = null;

    /**
     * @ORM\Column(type="datetime")
     */
    private $added;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Please enter a title.")
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="Incwadi\Core\Entity\Author", inversedBy="books", cascade={"persist"})
     * @Assert\NotBlank(message="Please enter an author.")
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="Genre")
     * Assert\NotBlank(message="Please enter a genre.")
     */
    private $genre;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Assert\NotBlank(message="Please set a price.")
     * @Assert\Type(type="float", message="Please enter a decimal.")
     * @Assert\GreaterThanOrEqual(0.00)
     */
    private $price;

    /**
     * @ORM\Column(type="boolean")
     */
    private $sold = false;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $soldOn = null;

    /**
     * @ORM\Column(type="boolean")
     */
    private $removed = false;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $removedOn = null;

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @Assert\Length(min=4, max=4, minMessage="The Year of publication must have four digits.", maxMessage="The Year of publication must have four digits.")
     * @Assert\NotBlank(message="Please enter the year of publication.")
     */
    private $releaseYear;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\Choice(choices=Book::TYPES, message="This type is not allowed.")
     */
    private $type = 'paperback';

    /**
     * @ORM\ManyToOne(targetEntity="Incwadi\Core\Entity\Customer", inversedBy="books")
     */
    private $lendTo;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lendOn = null;

    public function __construct()
    {
        $this->added = new \DateTime();
        $releaseYear = new \DateTime();
        $this->releaseYear = $releaseYear->format('Y');
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'branch' => $this->getBranch(),
            'added' => $this->getAdded()->getTimestamp(),
            'title' => $this->getTitle(),
            'author' => $this->getAuthor(),
            'genre' => $this->getGenre(),
            'price' => $this->getPrice(),
            'sold' => $this->getSold(),
            'soldOn' => $this->getSoldOn(),
            'removed' => $this->getRemoved(),
            'removedOn' => $this->getRemovedOn(),
            'releaseYear' => $this->getReleaseYear(),
            'type' => $this->getType(),
            'lendTo' => null !== $this->getLendTo() ? $this->getLendTo()->getId() : null,
            'lendOn' => null !== $this->getLendOn() ? $this->getLendOn()->getTimestamp() : null
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

    public function getAdded(): ?\DateTimeInterface
    {
        return $this->added;
    }

    public function setAdded(\DateTimeInterface $added): self
    {
        $this->added = $added;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function setAuthor(?Author $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getGenre(): ?Genre
    {
        return $this->genre;
    }

    public function setGenre(?Genre $genre): self
    {
        $this->genre = $genre;

        return $this;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getSold(): ?bool
    {
        return $this->sold;
    }

    public function setSold(bool $sold): self
    {
        $this->sold = $sold;

        return $this;
    }

    public function getSoldOn(): ?\DateTimeInterface
    {
        return $this->soldOn;
    }

    public function setSoldOn(?\DateTimeInterface $soldOn): self
    {
        $this->soldOn = $soldOn;

        return $this;
    }

    public function getRemoved(): ?bool
    {
        return $this->removed;
    }

    public function setRemoved(bool $removed): self
    {
        $this->removed = $removed;

        return $this;
    }

    public function getRemovedOn(): ?\DateTimeInterface
    {
        return $this->removedOn;
    }

    public function setRemovedOn(?\DateTimeInterface $removedOn): self
    {
        $this->removedOn = $removedOn;

        return $this;
    }

    public function getReleaseYear(): int
    {
        return $this->releaseYear;
    }

    public function setReleaseYear(int $releaseYear): self
    {
        $this->releaseYear = $releaseYear;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getLendTo(): ?Customer
    {
        return $this->lendTo;
    }

    public function setLendTo(?Customer $lendTo): self
    {
        $this->lendTo = $lendTo;

        return $this;
    }

    public function getLendOn(): ?\DateTimeInterface
    {
        return $this->lendOn;
    }

    public function setLendOn(?\DateTimeInterface $lendOn): self
    {
        $this->lendOn = $lendOn;

        return $this;
    }
}
