<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use App\Repository\ReservationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Validator\Constraints as Assert;

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

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    /**
     * @var Collection<Book>
     */
    #[ORM\OneToMany(targetEntity: Book::class, mappedBy: 'reservation')]
    private Collection $books;

    #[ORM\Column(type: 'string', length: 1, nullable: true)]
    #[Assert\Choice(['m', 'f', 'd'])]
    private $salutation;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $firstname;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $surname;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Email()]
    private $mail;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $phone;

    #[ORM\Column]
    private bool $open = true;

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
            'notes' => $this->getNotes(),
            'books' => $this->getBooks(),
            'salutation' => null !== $this->getSalutation() ? $this->getSalutation() : null,
            'firstname' => null !== $this->getFirstname() ? $this->getFirstname() : null,
            'surname' => null !== $this->getSurname() ? $this->getSurname() : null,
            'mail' => null !== $this->getMail() ? $this->getMail() : null,
            'phone' => null !== $this->getPhone() ? $this->getPhone() : null,
            'open' => $this->isOpen()
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

    public function getSalutation(): ?string
    {
        return $this->salutation;
    }

    public function setSalutation(?string $salutation): self
    {
        $this->salutation = $salutation;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(?string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function isOpen(): bool
    {
        return $this->open;
    }

    public function setOpen(bool $open): self
    {
        $this->open = $open;

        return $this;
    }
}
