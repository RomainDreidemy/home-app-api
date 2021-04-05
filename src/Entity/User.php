<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email()
     * @Assert\NotBlank
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=3, max=50, minMessage="Le nom doit avoir entre 3 et 50 caractères.")
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity=Home::class, mappedBy="user")
     */
    private $homes;

    /**
     * @ORM\OneToMany(targetEntity=ShoppingItem::class, mappedBy="user")
     */
    private $shoppingItems;

    /**
     * @ORM\OneToMany(targetEntity=Chore::class, mappedBy="user")
     */
    private $chores;

    public function __construct()
    {
        $this->homes = new ArrayCollection();
        $this->shoppingItems = new ArrayCollection();
        $this->chores = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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
     * @return Collection|Home[]
     */
    public function getHomes(): Collection
    {
        return $this->homes;
    }

    public function addHome(Home $home): self
    {
        if (!$this->homes->contains($home)) {
            $this->homes[] = $home;
            $home->addUser($this);
        }

        return $this;
    }

    public function removeHome(Home $home): self
    {
        if ($this->homes->removeElement($home)) {
            $home->removeUser($this);
        }

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
            $shoppingItem->setUser($this);
        }

        return $this;
    }

    public function removeShoppingItem(ShoppingItem $shoppingItem): self
    {
        if ($this->shoppingItems->removeElement($shoppingItem)) {
            // set the owning side to null (unless already changed)
            if ($shoppingItem->getUser() === $this) {
                $shoppingItem->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Chore[]
     */
    public function getChores(): Collection
    {
        return $this->chores;
    }

    public function addChore(Chore $chore): self
    {
        if (!$this->chores->contains($chore)) {
            $this->chores[] = $chore;
            $chore->setUser($this);
        }

        return $this;
    }

    public function removeChore(Chore $chore): self
    {
        if ($this->chores->removeElement($chore)) {
            // set the owning side to null (unless already changed)
            if ($chore->getUser() === $this) {
                $chore->setUser(null);
            }
        }

        return $this;
    }
}
