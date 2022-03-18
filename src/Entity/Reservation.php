<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use App\Repository\ReservationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;

#[Entity(repositoryClass: ReservationRepository::class)]
class Reservation implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'UUID')]
    #[ORM\Column(type: Types::GUID)]
    private $id;

    #[ORM\ManyToOne(targetEntity: Branch::class)]
    #[ORM\JoinColumn]
    private ?Branch $branch = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $collection = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    /**
     * @var Collection<Book>
     */
    /**
     * @var Collection<Book>
     */
    #[ORM\OneToMany(targetEntity: Book::class, mappedBy: 'reservation')]
    private Collection $books;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->books = new ArrayCollection();
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->getId(),
            'branch' => $this->getBranch(),
            'createdAt' => null !== $this->getCreatedAt() ? $this->getCreatedAt()->getTimestamp() : null,
            'collection' => null !== $this->getCollection() ? $this->getCollection()->getTimestamp() : null,
            'notes' => $this->getNotes(),
            'books' => $this->getBooks(),
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCollection(): ?\DateTimeInterface
    {
        return $this->collection;
    }

    public function setCollection(?\DateTimeInterface $collection): self
    {
        $this->collection = $collection;

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

    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function addBook(Book $book): self
    {
        if (!$this->books->contains($book)) {
            $this->books[] = $book;
            $book->setReservation($this);
        }

        return $this;
    }

    public function removeBook(Book $book): self
    {
        // set the owning side to null (unless already changed)
        if ($this->books->removeElement($book) && $book->getReservation() === $this) {
            $book->setReservation(null);
        }

        return $this;
    }
}
