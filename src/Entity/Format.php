<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping;
use App\Repository\FormatRepository;

#[\Doctrine\ORM\Mapping\Entity(repositoryClass: FormatRepository::class)]
class Format implements \JsonSerializable
{
    #[\Doctrine\ORM\Mapping\Id]
    #[\Doctrine\ORM\Mapping\GeneratedValue]
    #[\Doctrine\ORM\Mapping\Column(type: 'integer')]
    private $id;

    #[\Doctrine\ORM\Mapping\Column(type: 'string', length: 255)]
    private $name;

    #[\Doctrine\ORM\Mapping\ManyToOne(targetEntity: Branch::class)]
    private $branch;

    /**
     * @var \App\Entity\Book[]|\Doctrine\Common\Collections\Collection<int, \App\Entity\Book>
     */
    #[\Doctrine\ORM\Mapping\OneToMany(targetEntity: Book::class, mappedBy: 'format')]
    private $books;

    public function __construct()
    {
        $this->books = new ArrayCollection();
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'branch' => $this->getBranch(),
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

    public function getBranch(): ?Branch
    {
        return $this->branch;
    }

    public function setBranch(?Branch $branch): self
    {
        $this->branch = $branch;

        return $this;
    }

    /**
     * @return Collection|Book[]
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    /**
     * @param \App\Entity\Book[]|\Doctrine\Common\Collections\Collection<int, \App\Entity\Book> $book
     */
    public function addBook(array|\Doctrine\Common\Collections\Collection $book): self
    {
        if (!$this->books->contains($book)) {
            $this->books[] = $book;
            $book->setFormat($this);
        }

        return $this;
    }

    public function removeBook(Book $book): self
    {
        if ($this->books->removeElement($book) && $book->getFormat() === $this) {
            $book->setFormat(null);
        }

        return $this;
    }
}
