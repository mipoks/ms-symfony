<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 */
class Post
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;

    /**
     * @ORM\ManyToMany(targetEntity=Song::class)
     */
    private $song;

    /**
     * @ORM\ManyToOne(targetEntity=Person::class, inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $person;

    public function __construct()
    {
        $this->song = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return Collection|Song[]
     */
    public function getSong(): Collection
    {
        return $this->song;
    }

    public function addSong(Song $song): self
    {
        if (!$this->song->contains($song)) {
            $this->song[] = $song;
        }

        return $this;
    }

    public function removeSong(Song $song): self
    {
        $this->song->removeElement($song);

        return $this;
    }

    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function setPerson(?Person $person): self
    {
        $this->person = $person;

        return $this;
    }
}
