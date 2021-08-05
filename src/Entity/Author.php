<?php

namespace App\Entity;

use Doctrine\ORM\Mapping;
use App\Repository\AuthorRepository;
use Symfony\Component\Validator\Constraints;

#[\Doctrine\ORM\Mapping\Entity(repositoryClass: AuthorRepository::class)]
class Author implements \JsonSerializable
{
    #[\Doctrine\ORM\Mapping\Id]
    #[\Doctrine\ORM\Mapping\GeneratedValue]
    #[\Doctrine\ORM\Mapping\Column(type: 'integer')]
    private int $id;

    #[\Symfony\Component\Validator\Constraints\NotNull]
    #[\Doctrine\ORM\Mapping\Column(type: 'string', length: 255)]
    private string $firstname = '';

    #[\Symfony\Component\Validator\Constraints\NotBlank]
    #[\Doctrine\ORM\Mapping\Column(type: 'string', length: 255)]
    private string $surname = '';

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'firstname' => $this->getFirstname(),
            'surname' => $this->getSurname(),
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }
}
