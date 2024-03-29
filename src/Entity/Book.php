<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Validator\Constraints as Assert;

#[Entity(repositoryClass: BookRepository::class)]
class Book implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'UUID')]
    #[ORM\Column(type: Types::GUID)]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Branch::class)]
    private ?Branch $branch = null;

    /**
     * @var \DateTime|\DateTimeImmutable
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $added;

    #[Assert\NotBlank]
    #[ORM\Column(type: Types::STRING, length: '255')]
    private string $title = '';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $shortDescription = null;

    #[ORM\ManyToOne(targetEntity: Author::class, inversedBy: 'books', cascade: ['persist'])]
    private ?Author $author = null;

    #[ORM\ManyToOne(targetEntity: Genre::class, inversedBy: 'books')]
    private ?Genre $genre = null;

    #[Assert\Type(type: 'float')]
    #[Assert\GreaterThanOrEqual(0.00)]
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private float $price = 0.00;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $sold = false;

    /**
     * @var \DateTime|\DateTimeImmutable|null
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $soldOn = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $removed = false;

    /**
     * @var \DateTime|\DateTimeImmutable|null
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $removedOn = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $reserved = false;

    /**
     * @var \DateTime|\DateTimeImmutable|null
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $reservedAt = null;

    #[Assert\Length(max: 4)]
    #[ORM\Column(type: Types::INTEGER)]
    private int $releaseYear;

    #[ORM\ManyToOne(targetEntity: Condition::class)]
    private ?Condition $cond = null;

    /**
     * @var Collection<Tag>
     */
    /**
     * @var Collection<Tag>
     */
    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'books')]
    private Collection $tags;

    #[ORM\ManyToOne(targetEntity: Reservation::class, inversedBy: 'books')]
    private ?Reservation $reservation = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $recommendation = false;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    private $inventory;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: Format::class, inversedBy: 'books')]
    private ?Format $format = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $subtitle = null;

    #[ORM\Column]
    private bool $duplicate = false;

    public function __construct()
    {
        $this->added = new \DateTime();
        $releaseYear = new \DateTime();
        $this->releaseYear = $releaseYear->format('Y');
        $this->tags = new ArrayCollection();
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->getId(),
            'branch' => $this->getBranch(),
            'added' => $this->getAdded()->getTimestamp(),
            'title' => $this->getTitle(),
            'shortDescription' => $this->getShortDescription(),
            'author' => $this->getAuthor(),
            'genre' => $this->getGenre(),
            'price' => $this->getPrice(),
            'sold' => $this->getSold(),
            'soldOn' => null !== $this->getSoldOn() ? $this->getSoldOn()->getTimestamp() : null,
            'removed' => $this->getRemoved(),
            'removedOn' => null !== $this->getRemovedOn() ? $this->getRemovedOn()->getTimestamp() : null,
            'reserved' => $this->getReserved(),
            'reservedAt' => null !== $this->getReservedAt() ? $this->getReservedAt()->getTimestamp() : null,
            'releaseYear' => $this->getReleaseYear(),
            'condition' => $this->getCond(),
            'tags' => $this->getTags(),
            'reservation_id' => null !== $this->getReservation() ? $this->getReservation()->getId() : null,
            'recommendation' => $this->getRecommendation(),
            'inventory' => $this->getInventory(),
            'format' => $this->getFormat(),
            'subtitle' => $this->getSubtitle(),
            'duplicate' => $this->isDuplicate()
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

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(?string $shortDescription): self
    {
        $this->shortDescription = $shortDescription;

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

    public function getReserved(): ?bool
    {
        return $this->reserved;
    }

    public function setReserved(bool $reserved): self
    {
        $this->reserved = $reserved;

        return $this;
    }

    public function getReservedAt(): ?\DateTimeInterface
    {
        return $this->reservedAt;
    }

    public function setReservedAt(?\DateTimeInterface $reservedAt): self
    {
        $this->reservedAt = $reservedAt;

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

    public function getReservation(): ?Reservation
    {
        return $this->reservation;
    }

    public function setReservation(?Reservation $reservation): self
    {
        $this->reservation = $reservation;

        return $this;
    }

    public function getRecommendation(): ?bool
    {
        return $this->recommendation;
    }

    public function setRecommendation(bool $recommendation): self
    {
        $this->recommendation = $recommendation;

        return $this;
    }

    public function getInventory(): ?bool
    {
        return $this->inventory;
    }

    public function setInventory(?bool $inventory): self
    {
        $this->inventory = $inventory;

        return $this;
    }

    public function getFormat(): ?Format
    {
        return $this->format;
    }

    public function setFormat(?Format $format): self
    {
        $this->format = $format;

        return $this;
    }

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setSubtitle(?string $subtitle): self
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    public function isDuplicate(): bool
    {
        return $this->duplicate;
    }

    public function setDuplicate(bool $duplicate = false): self
    {
        $this->duplicate = $duplicate;

        return $this;
    }
}
