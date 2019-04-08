<?php

/*
 * This script is part of baldeweg/incwadi-core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Baldeweg\Entity;

use Baldeweg\Entity\Genre;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Baldeweg\Repository\BookRepository")
 */
class Book implements \JsonSerializable
{
    /**
     * @var array
     */
    const TYPES = ['paperback', 'hardcover'];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Baldeweg\Entity\Branch")
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
     * @ORM\Column(type="string", length=255, nullable=true)
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
     */
    private $price;

    /**
     * @ORM\Column(type="boolean")
     * @Assert\Type(type="boolean", message="Please enter only true or false.")
     */
    private $stocked = true;

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @Assert\Length(min=4, max=4, minMessage="The Year of publication must have four digits.", maxMessage="The Year of publication must have four digits.")
     * @Assert\NotBlank(message="Please enter the year of publication.")
     */
    private $yearOfPublication;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\Choice(choices=Book::TYPES, message="This type is not allowed.")
     */
    private $type = 'paperback';

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     * @Assert\Type(type="boolean")
     */
    private $premium = false;

    /**
     * @ORM\OneToOne(targetEntity="Baldeweg\Entity\Lending", mappedBy="book", cascade={"persist", "remove"})
     */
    private $lending;


    public function __construct()
    {
        $this->added = new \DateTime();
        $yearOfPublication = new \DateTime();
        $this->yearOfPublication = $yearOfPublication->format('Y');
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
            'stocked' => $this->getStocked(),
            'yearOfPublication' => $this->getYearOfPublication(),
            'type' => $this->getType(),
            'premium' => $this->getPremium(),
            'lending' => $this->getLending() ? true : false
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

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(?string $author): self
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

    public function getStocked(): ?bool
    {
        return $this->stocked;
    }

    public function setStocked(bool $stocked): self
    {
        $this->stocked = $stocked;

        return $this;
    }

    public function getYearOfPublication(): int
    {
        return $this->yearOfPublication;
    }

    public function setYearOfPublication(int $yearOfPublication): self
    {
        $this->yearOfPublication = $yearOfPublication;

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

    public function getPremium(): bool
    {
        return $this->premium;
    }

    public function setPremium(bool $premium): self
    {
        $this->premium = $premium;

        return $this;
    }

    public function getLending(): ?Lending
    {
        return $this->lending;
    }

    public function setLending(Lending $lending): self
    {
        $this->lending = $lending;

        if ($this !== $lending->getBook()) {
            $lending->setBook($this);
        }

        return $this;
    }
}
