<?php

namespace App\Entity;

use App\Repository\HomeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=HomeRepository::class)
 */
class Home
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     * @Groups("home")
     * @Assert\NotBlank()
     * @Assert\Length(min=3, max=30, minMessage="Le nom doit contenir entre 3 et 30 caractères", maxMessage="Le nom doit contenir entre 3 et 30 caractères")
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="homes", cascade="persist")
     * @Assert\Count(
     *      min = 1,
     *      minMessage = "La maison doit avoir un moins un personne.",
     * )
     */
    private $user;

    /**
     * @ORM\Column(type="boolean")
     */
    private $state;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $share_code;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $share_code_expiration;

    /**
     * @ORM\OneToMany(targetEntity=ShoppingList::class, mappedBy="home")
     * @Groups("shopping_list_details")
     *
     * @MaxDepth(1)
     */
    private $shoppingLists;

    public function __construct()
    {
        $this->User = new ArrayCollection();
        $this->user = new ArrayCollection();
        $this->shoppingLists = new ArrayCollection();
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

    /**
     * @return Collection|User[]
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user[] = $user;
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->user->removeElement($user);

        return $this;
    }

    public function getState(): ?bool
    {
        return $this->state;
    }

    public function setState(bool $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getShareCode(): ?string
    {
        return $this->share_code;
    }

    public function setShareCode(?string $share_code): self
    {
        $this->share_code = $share_code;

        return $this;
    }

    public function getShareCodeExpiration(): ?\DateTimeInterface
    {
        return $this->share_code_expiration;
    }

    public function setShareCodeExpiration(?\DateTimeInterface $share_code_expiration): self
    {
        $this->share_code_expiration = $share_code_expiration;

        return $this;
    }

    /**
     * @return Collection|ShoppingList[]
     */
    public function getShoppingLists(): Collection
    {
        return $this->shoppingLists;
    }

    public function addShoppingList(ShoppingList $shoppingList): self
    {
        if (!$this->shoppingLists->contains($shoppingList)) {
            $this->shoppingLists[] = $shoppingList;
            $shoppingList->setHome($this);
        }

        return $this;
    }

    public function removeShoppingList(ShoppingList $shoppingList): self
    {
        if ($this->shoppingLists->removeElement($shoppingList)) {
            // set the owning side to null (unless already changed)
            if ($shoppingList->getHome() === $this) {
                $shoppingList->setHome(null);
            }
        }

        return $this;
    }
}
