<?php

namespace App\Entity;

use App\Repository\RunnerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
#[ApiResource]

#[ORM\Entity(repositoryClass: RunnerRepository::class)]
class Runner
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'runners')]
    private ?Race $id_race = null;

    /**
     * @var Collection<int, ScanLog>
     */
    #[ORM\OneToMany(targetEntity: ScanLog::class, mappedBy: 'id_runner')]
    private Collection $scanLogs;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $date_start = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $date_end = null;

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

    public function getIdRace(): ?Race
    {
        return $this->id_race;
    }

    public function setIdRace(?Race $id_race): static
    {
        $this->id_race = $id_race;

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
            $scanLog->setIdRunner($this);
        }

        return $this;
    }

    public function removeScanLog(ScanLog $scanLog): static
    {
        if ($this->scanLogs->removeElement($scanLog)) {
            // set the owning side to null (unless already changed)
            if ($scanLog->getIdRunner() === $this) {
                $scanLog->setIdRunner(null);
            }
        }

        return $this;
    }

    public function getDateStart(): ?\DateTime
    {
        return $this->date_start;
    }

    public function setDateStart(?\DateTime $date_start): static
    {
        $this->date_start = $date_start;

        return $this;
    }

    public function getDateEnd(): ?\DateTime
    {
        return $this->date_end;
    }

    public function setDateEnd(?\DateTime $date_end): static
    {
        $this->date_end = $date_end;

        return $this;
    }
}
