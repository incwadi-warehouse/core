<?php

/*
 * This script is part of incwadi/core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Incwadi\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Incwadi\Core\Repository\LendingRepository")
 */
class Lending implements \JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Incwadi\Core\Entity\Customer")
     * @ORM\JoinColumn(nullable=false)
     */
    private $customer;

    /**
     * @ORM\OneToOne(targetEntity="Incwadi\Core\Entity\Book")
     * @ORM\JoinColumn(nullable=false)
     */
    private $book;

    /**
     * @ORM\Column(type="datetime")
     */
    private $lendOn;


    public function __construct()
    {
        $this->lendOn = new \DateTime();
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'customer' => $this->getCustomer(),
            'book' => $this->getBook(),
            'lendOn' => $this->getLendOn()->format('d.m.Y H:i')
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(Book $book): self
    {
        $this->book = $book;

        return $this;
    }

    public function getLendOn(): ?\DateTimeInterface
    {
        return $this->lendOn;
    }

    public function setLendOn(\DateTimeInterface $lendOn): self
    {
        $this->lendOn = $lendOn;

        return $this;
    }
}
