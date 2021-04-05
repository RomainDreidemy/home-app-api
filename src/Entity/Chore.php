<?php

namespace App\Entity;

use App\Repository\ChoreRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ChoreRepository::class)
 */
class Chore
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=3, max=50)
     */
    private ?string $name;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(min = 1, max = 3, notInRangeMessage = "Une tâche peut avoir entre {{ min }} et {{ max }} points")
     */
    private ?int $point;

    /**
     * @ORM\ManyToOne(targetEntity=Home::class, inversedBy="chores")
     * @Assert\NotNull
     */
    private ?Home $home;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="chores")
     */
    private ?User $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPoint(): ?int
    {
        return $this->point;
    }

    public function setPoint(int $point): self
    {
        $this->point = $point;

        return $this;
    }

    public function getHome(): ?Home
    {
        return $this->home;
    }

    public function setHome(?Home $home): self
    {
        $this->home = $home;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
