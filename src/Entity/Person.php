<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PersonRepository")
 * @ORM\Table(name="persons")
 */
class Person implements UserInterface
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(min=2, max=30)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @var array
     *
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $songCount = 5;

    /**
     * Person constructor.
     * @param string $email
     * @param string $name
     * @param string $password
     */
    public function __construct(string $name, string $email)
    {
        $this->email = $email;
        $this->name = $name;
        $this->posts = new ArrayCollection();
        $this->songs = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getSongCount(): int
    {
        return $this->songCount;
    }

    /**
     * @param int $songCount
     */
    public function setSongCount(int $songCount): void
    {
        $this->songCount = $songCount;
    }

    /**
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="person", orphanRemoval=true)
     */
    private $posts;

    /**
     * @ORM\ManyToMany(targetEntity=Song::class, fetch="EAGER")
     */
    private $songs;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }


    /**
     * Returns the roles or permissions granted to the user for security.
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        // guarantees that a user always has at least one role for security
        if (empty($roles)) {
            $roles[] = 'ROLE_USER';
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function serialize(): string
    {
        // add $this->salt too if you don't use Bcrypt or Argon2i
        return serialize([$this->id, $this->name, $this->password]);
    }

    public function unserialize($serialized): void
    {
        // add $this->salt too if you don't use Bcrypt or Argon2i
        [$this->id, $this->name, $this->password] = unserialize($serialized, ['allowed_classes' => false]);
    }

    public function getSalt()
    {
        return null;
        // TODO: Implement getSalt() method.
    }

    public function getUsername()
    {
        return $this->name;
        // TODO: Implement getUsername() method.
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setPerson($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getPerson() === $this) {
                $post->setPerson(null);
            }
        }

        return $this;
    }

    public function hasSong(Song $songCheck) : bool{
        foreach ($this->getSongs() as $song) {
            if ($song->getId() == $songCheck->getId()) {
                return true;
            }
        }
        return false;
    }

    public function removeSong(Song $songToRemove) : bool{
        $songsArray = $this->getSongs();
        for ($i = 0; $i < count($songsArray); $i++) {
            if ($songsArray[$i]->getId() == $songToRemove->getId()) {
                unset($songsArray[$i]);
                return false;
            }
        }
        return false;
    }
    public function addSong(Song $songToRemove) : bool{
        return $this->songs->add($songToRemove);
    }

    /**
     * @return Collection|Song[]
     */
    public function getSongs(): Collection
    {
        return $this->songs;
    }

}
