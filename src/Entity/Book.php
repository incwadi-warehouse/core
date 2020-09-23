<?php

/*
 * This script is part of incwadi/core
 */

namespace Incwadi\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Incwadi\Core\Repository\BookRepository;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=BookRepository::class)
 */
class Book implements \JsonSerializable
{
    /**
     * @var string[]
     */
    const TYPES = ['paperback', 'hardcover'];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    private string $id;

    /**
     * @ORM\ManyToOne(targetEntity=Branch::class)
     */
    private ?Branch $branch = null;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTime $added;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private string $title = '';

    /**
     * @ORM\ManyToOne(targetEntity=Author::class, inversedBy="books", cascade={"persist"})
     * @Assert\NotBlank()
     */
    private ?Author $author = null;

    /**
     * @ORM\ManyToOne(targetEntity=Genre::class, inversedBy="books")
     * Assert\NotBlank()
     */
    private ?Genre $genre = null;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Assert\NotBlank()
     * @Assert\Type(type="float")
     * @Assert\GreaterThanOrEqual(0.00)
     */
    private float $price = 0.00;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $sold = false;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTime $soldOn = null;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $removed = false;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTime $removedOn = null;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Length(min=4, max=4)
     * @Assert\NotBlank()
     */
    private int $releaseYear;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Choice(choices=Book::TYPES)
     */
    private string $type = 'paperback';

    /**
     * @ORM\ManyToOne(targetEntity=Staff::class, inversedBy="books")
     */
    private ?Staff $lendTo = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTime $lendOn = null;

    /**
     * @ORM\ManyToOne(targetEntity=Condition::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private ?Condition $cond = null;

    /**
     * @ORM\ManyToMany(targetEntity=Tag::class, inversedBy="books")
     */
    private $tags;

    public function __construct()
    {
        $this->added = new \DateTime();
        $releaseYear = new \DateTime();
        $this->releaseYear = $releaseYear->format('Y');
        $this->tags = new ArrayCollection();
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
            'soldOn' => $this->getSoldOn() ? $this->getSoldOn()->getTimestamp() : null,
            'removed' => $this->getRemoved(),
            'removedOn' => $this->getRemovedOn() ? $this->getRemovedOn()->getTimestamp() : null,
            'releaseYear' => $this->getReleaseYear(),
            'type' => $this->getType(),
            'lendTo' => null !== $this->getLendTo() ? $this->getLendTo()->getId() : null,
            'lendOn' => null !== $this->getLendOn() ? $this->getLendOn()->getTimestamp() : null,
            'condition' => $this->getCond(),
            'tags' => $this->getTags(),
        ];
    }

    public function getId(): ?string
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

    public function getLendTo(): ?Staff
    {
        return $this->lendTo;
    }

    public function setLendTo(?Staff $lendTo): self
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

    public function getCond(): ?Condition
    {
        return $this->cond;
    }

    public function setCond(?Condition $cond): self
    {
        $this->cond = $cond;

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }

        return $this;
    }
}
