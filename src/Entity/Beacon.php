<?php

namespace App\Entity;

use App\Repository\BeaconRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Map;
use ApiPlatform\Metadata\ApiResource;
#[ApiResource]

#[ORM\Entity(repositoryClass: BeaconRepository::class)]
class Beacon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 7, nullable: true)]
    private ?string $longitude = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 7, nullable: true)]
    private ?string $latitude = null;

    #[ORM\Column]
    private ?bool $isPlaced = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTime $placedAt = null;

    #[ORM\Column]
    private ?\DateTime $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'beacons')]
    private ?map $id_map = null;

    /**
     * @var Collection<int, ScanLog>
     */
    #[ORM\OneToMany(targetEntity: ScanLog::class, mappedBy: 'id_beacon')]
    private Collection $scanLogs;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $qr_code = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $accepted_distance = null;

    public function __construct()
    {
        $this->scanLogs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function isPlaced(): ?bool
    {
        return $this->isPlaced;
    }

    public function setIsPlaced(bool $isPlaced): static
    {
        $this->isPlaced = $isPlaced;

        return $this;
    }

    public function getPlacedAt(): ?\DateTime
    {
        return $this->placedAt;
    }

    public function setPlacedAt(\DateTime $placedAt): static
    {
        $this->placedAt = $placedAt;

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

    public function getIdMap(): ?map
    {
        return $this->id_map;
    }

    public function setIdMap(?map $id_map): static
    {
        $this->id_map = $id_map;

        return $this;
    }

    /**
     * @return Collection<int, ScanLog>
     */
    public function getScanLogs(): Collection
    {
        return $this->scanLogs;
    }

    public function addScanLog(ScanLog $scanLog): static
    {
        if (!$this->scanLogs->contains($scanLog)) {
            $this->scanLogs->add($scanLog);
            $scanLog->setIdBeacon($this);
        }

        return $this;
    }

    public function removeScanLog(ScanLog $scanLog): static
    {
        if ($this->scanLogs->removeElement($scanLog)) {
            // set the owning side to null (unless already changed)
            if ($scanLog->getIdBeacon() === $this) {
                $scanLog->setIdBeacon(null);
            }
        }

        return $this;
    }

    public function getQrCode(): ?string
    {
        return $this->qr_code;
    }

    public function setQrCode(?string $qr_code): static
    {
        $this->qr_code = $qr_code;

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

    public function getAcceptedDistance(): ?string
    {
        return $this->accepted_distance;
    }

    public function setAcceptedDistance(?string $accepted_distance): static
    {
        $this->accepted_distance = $accepted_distance;

        return $this;
    }

}
