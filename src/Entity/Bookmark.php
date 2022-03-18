<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use App\Repository\BookmarkRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BookmarkRepository::class)]
class Bookmark implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'UUID')]
    #[ORM\Column(type: Types::GUID)]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Branch::class)]
    private Branch $branch;

    #[Assert\Url]
    #[ORM\Column(type: Types::STRING, length: '255')]
    private string $url = '';

    #[ORM\Column(type: Types::STRING, length: '255', nullable: true)]
    private ?string $name = null;

    public function jsonSerialize(): mixed
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
