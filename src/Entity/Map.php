<?php

namespace App\Entity;

use App\Repository\MapRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MapRepository::class)]
class Map
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name_map = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column]
    private ?\DateTime $createdAt = null;

    /**
     * @var Collection<int, Race>
     */
    #[ORM\OneToMany(targetEntity: Race::class, mappedBy: 'id_map')]
    private Collection $races;

    #[ORM\ManyToOne(inversedBy: 'maps')]
    private ?user $id_user = null;

    /**
     * @var Collection<int, Beacon>
     */
    #[ORM\OneToMany(targetEntity: Beacon::class, mappedBy: 'id_map')]
    private Collection $beacons;

    public function __construct()
    {
        $this->races = new ArrayCollection();
        $this->beacons = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameMap(): ?string
    {
        return $this->name_map;
    }

    public function setNameMap(string $name_map): static
    {
        $this->name_map = $name_map;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, Race>
     */
    public function getRaces(): Collection
    {
        return $this->races;
    }

    public function addRace(Race $race): static
    {
        if (!$this->races->contains($race)) {
            $this->races->add($race);
            $race->setIdMap($this);
        }

        return $this;
    }

    public function removeRace(Race $race): static
    {
        if ($this->races->removeElement($race)) {
            // set the owning side to null (unless already changed)
            if ($race->getIdMap() === $this) {
                $race->setIdMap(null);
            }
        }

        return $this;
    }

    public function getIdUser(): ?user
    {
        return $this->id_user;
    }

    public function setIdUser(?user $id_user): static
    {
        $this->id_user = $id_user;

        return $this;
    }

    /**
     * @return Collection<int, Beacon>
     */
    public function getBeacons(): Collection
    {
        return $this->beacons;
    }

    public function addBeacon(Beacon $beacon): static
    {
        if (!$this->beacons->contains($beacon)) {
            $this->beacons->add($beacon);
            $beacon->setIdMap($this);
        }

        return $this;
    }

    public function removeBeacon(Beacon $beacon): static
    {
        if ($this->beacons->removeElement($beacon)) {
            // set the owning side to null (unless already changed)
            if ($beacon->getIdMap() === $this) {
                $beacon->setIdMap(null);
            }
        }

        return $this;
    }
}
