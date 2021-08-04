<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\SavedSearchRepository;

/**
 * @ORM\Entity(repositoryClass=SavedSearchRepository::class)
 */
class SavedSearch implements \JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity=Branch::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private Branch $branch;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $name;

    /**
     * @ORM\Column(type="json")
     */
    private array $query = [];

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'branch' => $this->getBranch(),
            'name' => $this->getName(),
            'query' => $this->getQuery(),
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getQuery(): ?array
    {
        return $this->query;
    }

    public function setQuery(array $query): self
    {
        $this->query = $query;

        return $this;
    }
}
