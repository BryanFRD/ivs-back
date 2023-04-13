<?php

namespace App\Entity;

use App\Repository\OrganizationRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Symfony\Bridge\Doctrine\IdGenerator\UlidGenerator;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity(repositoryClass: OrganizationRepository::class)]
class Organization implements JsonSerializable {
    
    #[ORM\Id]
    #[ORM\GeneratedValue("CUSTOM")]
    #[ORM\CustomIdGenerator(class: UlidGenerator::class)]
    #[ORM\Column(type: UlidType::NAME, unique: true)]
    private ?Ulid $id = null;
    
    #[ORM\Column(length: 255)]
    private ?string $name = null;
    
    #[ORM\OneToMany(targetEntity: Building::class, mappedBy: "organization")]
    private Collection $buildings;
    
    public function getId(): Ulid {
        return $this->id;
    }
    
    public function getName(): ?string {
        return $this->name;
    }
    
    public function setName(string $name): self {
        $this->name = $name;
        return $this;
    }
    
    public function getBuildings(): Collection {
        return $this->buildings;
    }
    
    public function jsonSerialize(): mixed {
        return array(
            "id" => $this->getId(),
            "name" => $this->getName(),
            "buildings" => $this->getBuildings()
        );
    }
    
}
