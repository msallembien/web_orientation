<?php

namespace App\Entity;

use App\Repository\ScanLogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
#[ApiResource]
#[ORM\Entity(repositoryClass: ScanLogRepository::class)]
class ScanLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $scan_at = null;

    #[ORM\ManyToOne(inversedBy: 'scanLogs')]
    private ?Runner $id_runner = null;

    #[ORM\ManyToOne(inversedBy: 'scanLogs')]
    private ?Beacon $id_beacon = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 7)]
    private ?string $longitude = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 7)]
    private ?string $latitude = null;

    #[ORM\Column]
    private ?bool $is_valid = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScanAt(): ?\DateTimeImmutable
    {
        return $this->scan_at;
    }

    public function setScanAt(?\DateTimeImmutable $scan_at): static
    {
        $this->scan_at = $scan_at;

        return $this;
    }

    public function getIdRunner(): ?Runner
    {
        return $this->id_runner;
    }

    public function setIdRunner(?Runner $id_runner): static
    {
        $this->id_runner = $id_runner;

        return $this;
    }

    public function getIdBeacon(): ?Beacon
    {
        return $this->id_beacon;
    }

    public function setIdBeacon(?Beacon $id_beacon): static
    {
        $this->id_beacon = $id_beacon;

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

    public function isValid(): ?bool
    {
        return $this->is_valid;
    }

    public function setIsValid(bool $is_valid): static
    {
        $this->is_valid = $is_valid;

        return $this;
    }
}
