<?php

namespace App\Entity;

use Doctrine\ORM\Mapping;
use App\Repository\StaffRepository;
use Symfony\Component\Validator\Constraints;

#[\Doctrine\ORM\Mapping\Entity(repositoryClass: StaffRepository::class)]
class Staff implements \JsonSerializable
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
}
