<?php

namespace App\Entity;

use App\Repository\RaceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RaceRepository::class)]
class Race
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $race_name = null;

    #[ORM\Column]
    private ?int $nbRunner = null;

    #[ORM\ManyToOne(inversedBy: 'races')]
    private ?map $id_map = null;

    /**
     * @var Collection<int, Runner>
     */
    #[ORM\OneToMany(targetEntity: Runner::class, mappedBy: 'id_race')]
    private Collection $runners;

    public function __construct()
    {
        $this->runners = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRaceName(): ?string
    {
        return $this->race_name;
    }

    public function setRaceName(string $race_name): static
    {
        $this->race_name = $race_name;

        return $this;
    }

    public function getNbRunner(): ?int
    {
        return $this->nbRunner;
    }

    public function setNbRunner(int $nbRunner): static
    {
        $this->nbRunner = $nbRunner;

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
     * @return Collection<int, Runner>
     */
    public function getRunners(): Collection
    {
        return $this->runners;
    }

    public function addRunner(Runner $runner): static
    {
        if (!$this->runners->contains($runner)) {
            $this->runners->add($runner);
            $runner->setIdRace($this);
        }

        return $this;
    }

    public function removeRunner(Runner $runner): static
    {
        if ($this->runners->removeElement($runner)) {
            // set the owning side to null (unless already changed)
            if ($runner->getIdRace() === $this) {
                $runner->setIdRace(null);
            }
        }

        return $this;
    }
}
