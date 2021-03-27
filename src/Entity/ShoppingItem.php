<?php

namespace App\Entity;

use App\Repository\ShoppingItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ShoppingItemRepository::class)
 */
class ShoppingItem
{
    public function __clone(): void
    {
        $this->id = null;
        $this->buy = false;
        $this->shoppingList = null;
    }

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\Length(min=2, max=50, minMessage="Le nom doit contenir au moins {{ limit }} caractères.", maxMessage="Le nom doit contenir {{ limit }} caractères maximum.")
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $buy;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="shoppingItems")
     * @Assert\NotNull(message="Un item doit appartenir à un utilisateur.")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=ShoppingList::class, inversedBy="shoppingItems")
     * @Assert\NotNull(message="Un item doit appartenir à une liste de course.")
     */
    private $shoppingList;

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

    public function getBuy(): ?bool
    {
        return $this->buy;
    }

    public function setBuy(bool $buy): self
    {
        $this->buy = $buy;

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

    public function getShoppingList(): ?ShoppingList
    {
        return $this->shoppingList;
    }

    public function setShoppingList(?ShoppingList $shoppingList): self
    {
        $this->shoppingList = $shoppingList;

        return $this;
    }
}
