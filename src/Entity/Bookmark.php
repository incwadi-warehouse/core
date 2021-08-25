<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\BookmarkRepository;
use App\Entity\Branch;

#[ORM\Entity(repositoryClass: BookmarkRepository::class)]
class Bookmark implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'UUID')]
    #[ORM\Column(type: 'guid')]
    private string $id;


    #[ORM\ManyToOne(targetEntity: Branch::class)]
    private Branch $branch;


    #[Assert\Url]
    #[ORM\Column(type: 'string', length: '255')]
    private string $url = '';

    #[ORM\Column(type: 'string', length: '255', nullable: true)]
    private ?string $name = null;

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'url' => $this->getUrl(),
            'name' => $this->getName(),
        ];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setBranch(Branch $branch): Bookmark
    {
        $this->branch = $branch;

        return $this;
    }

    public function getBranch(): Branch
    {
        return $this->branch;
    }

    public function setUrl(string $url): Bookmark
    {
        $this->url = $url;

        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setName(string $name = null): Bookmark
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }
}
