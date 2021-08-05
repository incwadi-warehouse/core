<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping;
use App\Repository\GenreRepository;
use Symfony\Component\Validator\Constraints;

#[\Doctrine\ORM\Mapping\Entity(repositoryClass: GenreRepository::class)]
class Genre implements \JsonSerializable
{
    #[\Doctrine\ORM\Mapping\Id]
    #[\Doctrine\ORM\Mapping\GeneratedValue]
    #[\Doctrine\ORM\Mapping\Column(type: 'integer')]
    private int $id;

    #[\Symfony\Component\Validator\Constraints\NotBlank]
    #[\Doctrine\ORM\Mapping\Column(type: 'string', length: 255)]
    private string $name = '';

    #[\Doctrine\ORM\Mapping\ManyToOne(targetEntity: Branch::class)]
    private $branch;

    /**
     * @var \App\Entity\Book[]|\Doctrine\Common\Collections\Collection<int, \App\Entity\Book>
     */
    #[\Doctrine\ORM\Mapping\OneToMany(targetEntity: Book::class, mappedBy: 'genre')]
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
            'books' => count($this->getBooks()),
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
            $book->setGenre($this);
        }

        return $this;
    }

    public function removeBook(Book $book): self
    {
        if ($this->books->contains($book)) {
            $this->books->removeElement($book);
            if ($book->getGenre() === $this) {
                $book->setGenre(null);
            }
        }

        return $this;
    }
}
