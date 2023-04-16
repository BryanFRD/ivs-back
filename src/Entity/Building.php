<?php

namespace App\Entity;

use App\Repository\BuildingRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Symfony\Bridge\Doctrine\IdGenerator\UlidGenerator;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity(repositoryClass: BuildingRepository::class)]
class Building implements JsonSerializable {
    
    #[ORM\Id]
    #[ORM\GeneratedValue("CUSTOM")]
    #[ORM\CustomIdGenerator(class: UlidGenerator::class)]
    #[ORM\Column(type: UlidType::NAME, unique: true)]
    private ?Ulid $id = null;
    
    #[ORM\Column(length: 255)]
    private ?string $name;
    
    #[ORM\Column]
    private ?int $zipcode;
    
    #[ORM\ManyToOne(targetEntity: Organization::class, inversedBy: "building")]
    private ?Organization $organization;
    
    #[ORM\OneToMany(targetEntity: Room::class, mappedBy: "buildings")]
    private ?Collection $rooms;
    
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
    
    public function getZipcode(): ?int {
        return $this->zipcode;
    }
    
    public function setZipcode(int $zipcode): self {
        $this->zipcode = $zipcode;
        return $this;
    }
    
    public function getOrganization() {
        return $this->organization;
    }
    
    public function setOrganization($organization): self {
        $this->organization = $organization;
        return $this;
    }
    
    public function getRooms(): ?Collection {
        return $this->rooms;
    }
    
    public function jsonSerialize(): mixed {
        return array(
            "id" => $this->getId(),
            "name" => $this->getName(),
            "zipcode" => $this->getZipcode(),
            "organization" => $this->getOrganization()
        );
    }
    
}
