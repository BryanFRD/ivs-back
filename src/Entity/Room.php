<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Symfony\Bridge\Doctrine\IdGenerator\UlidGenerator;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity(repositoryClass: RoomRepository::class)]
class Room implements JsonSerializable {
    
    #[ORM\Id]
    #[ORM\GeneratedValue("CUSTOM")]
    #[ORM\CustomIdGenerator(class: UlidGenerator::class)]
    #[ORM\Column(type: UlidType::NAME, unique: true)]
    private ?Ulid $id = null;
    
    #[ORM\Column(length: 255)]
    private ?string $name = null;
    
    #[ORM\Column]
    private ?int $people = null;
    
    public function getId(): ?Ulid {
        return $this->id;
    }
    
    public function getName(): ?string {
        return $this->name;
    }
    
    public function setName(string $name): self {
        $this->name = $name;
        return $this;
    }

    public function getPeople(): ?int {
        return $this->people;
    }
    
    public function setPeople(int $people): self {
        $this->people = $people;
        return $this;
    }
    
    public function jsonSerialize(): mixed {
        return array(
            "id" => $this->getId(),
            "name" => $this->getName(),
            "people" => $this->getPeople()
        );
    }
    
}
