<?php

namespace Incwadi\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Incwadi\Core\Repository\FormatRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FormatRepository::class)
 */
class Format implements \JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=Branch::class)
     */
    private $branch;

    /**
     * @ORM\OneToMany(targetEntity=Book::class, mappedBy="format")
     */
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

    public function addBook(Book $book): self
    {
        if (!$this->books->contains($book)) {
            $this->books[] = $book;
            $book->setFormat($this);
        }

        return $this;
    }

    public function removeBook(Book $book): self
    {
        if ($this->books->removeElement($book)) {
            if ($book->getFormat() === $this) {
                $book->setFormat(null);
            }
        }

        return $this;
    }
}
