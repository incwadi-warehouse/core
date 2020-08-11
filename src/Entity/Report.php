<?php

/*
 * This script is part of incwadi/core
 */

namespace Incwadi\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use Incwadi\Core\Repository\ReportRepository;

/**
 * @ORM\Entity(repositoryClass=ReportRepository::class)
 */
class Report implements \JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Branch::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $branch;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $searchTerm;

    /**
     * @ORM\Column(type="integer")
     */
    private $limitTo = 50;

    /**
     * @ORM\Column(type="boolean")
     */
    private $sold = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $removed = false;

    /**
     * @ORM\Column(name="older_then_x_months", type="integer", nullable=true)
     */
    private $olderThenXMonths = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $branches = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $genres = null;

    /**
     * @ORM\Column(name="lend_more_then_x_months", type="integer", nullable=true)
     */
    private $lendMoreThenXMonths = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $orderBy = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $releaseYear = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type = null;

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'branch' => $this->getBranch(),
            'name' => $this->getName(),
            'searchTerm' => $this->getSearchTerm(),
            'limitTo' => $this->getLimitTo(),
            'sold' => $this->getSold(),
            'removed' => $this->getRemoved(),
            'olderThenXMonths' => $this->getOlderThenXMonths(),
            'branches' => $this->getBranches(),
            'genres' => $this->getGenres(),
            'lendMoreThenXMonths' => $this->getLendMoreThenXMonths(),
            'orderBy' => $this->getOrderBy(),
            'releaseYear' => $this->getReleaseYear(),
            'type' => $this->getType(),
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

    public function getSearchTerm(): ?string
    {
        return $this->searchTerm;
    }

    public function setSearchTerm(?string $searchTerm): self
    {
        $this->searchTerm = $searchTerm;

        return $this;
    }

    public function getLimitTo(): ?int
    {
        return $this->limitTo;
    }

    public function setLimitTo(int $limitTo): self
    {
        $this->limitTo = $limitTo;

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

    public function getRemoved(): ?bool
    {
        return $this->removed;
    }

    public function setRemoved(bool $removed): self
    {
        $this->removed = $removed;

        return $this;
    }

    public function getOlderThenXMonths(): ?int
    {
        return $this->olderThenXMonths;
    }

    public function setOlderThenXMonths(?int $olderThenXMonths): self
    {
        $this->olderThenXMonths = $olderThenXMonths;

        return $this;
    }

    public function getBranches(): ?string
    {
        return $this->branches;
    }

    public function setBranches(?string $branches): self
    {
        $this->branches = $branches;

        return $this;
    }

    public function getGenres(): ?string
    {
        return $this->genres;
    }

    public function setGenres(?string $genres): self
    {
        $this->genres = $genres;

        return $this;
    }

    public function getLendMoreThenXMonths(): ?int
    {
        return $this->lendMoreThenXMonths;
    }

    public function setLendMoreThenXMonths(?int $lendMoreThenXMonths): self
    {
        $this->lendMoreThenXMonths = $lendMoreThenXMonths;

        return $this;
    }

    public function getOrderBy(): ?string
    {
        return $this->orderBy;
    }

    public function setOrderBy(?string $orderBy): self
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    public function getReleaseYear(): ?int
    {
        return $this->releaseYear;
    }

    public function setReleaseYear(?int $releaseYear): self
    {
        $this->releaseYear = $releaseYear;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
