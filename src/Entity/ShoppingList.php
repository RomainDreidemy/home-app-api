<?php

namespace App\Entity;

use App\Repository\ShoppingListRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ShoppingListRepository::class)
 */
class ShoppingList
{

    public function __clone(): void
    {
        $this->id = null;
        $this->created_at = new \DateTime('now');
        $this->modified_at = null;

        $oldShoppingItem = $this->shoppingItems;

        foreach ($oldShoppingItem as $item){
            $this->addShoppingItem(clone $item);
        }
    }

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     * @Groups("home")
     * @Assert\Length(min=3, max=30, minMessage="Le nom doit contenir entre 3 et 30 caractères", maxMessage="Le nom doit contenir entre 3 et 30 caractères")
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotNull
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $modified_at;

    /**
     * @ORM\ManyToOne(targetEntity=Home::class, inversedBy="shoppingLists")
     * @Assert\NotNull(message="La liste doit être attaché à une maison.")
     */
    private $home;

    /**
     * @ORM\OneToMany(targetEntity=ShoppingItem::class, mappedBy="shoppingList", cascade="persist")
     */
    private $shoppingItems;

    public function __construct()
    {
        $this->shoppingItems = new ArrayCollection();
    }

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getModifiedAt(): ?\DateTimeInterface
    {
        return $this->modified_at;
    }

    public function setModifiedAt(?\DateTimeInterface $modified_at): self
    {
        $this->modified_at = $modified_at;

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

    /**
     * @return Collection|ShoppingItem[]
     */
    public function getShoppingItems(): Collection
    {
        return $this->shoppingItems;
    }

    public function addShoppingItem(ShoppingItem $shoppingItem): self
    {
        if (!$this->shoppingItems->contains($shoppingItem)) {
            $this->shoppingItems[] = $shoppingItem;
            $shoppingItem->setShoppingList($this);
        }

        return $this;
    }

    public function removeShoppingItem(ShoppingItem $shoppingItem): self
    {
        if ($this->shoppingItems->removeElement($shoppingItem)) {
            // set the owning side to null (unless already changed)
            if ($shoppingItem->getShoppingList() === $this) {
                $shoppingItem->setShoppingList(null);
            }
        }

        return $this;
    }
}
