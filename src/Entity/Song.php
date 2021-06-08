<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use function Symfony\Component\String\u;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SongRepository")
 * @ORM\Table(name="songs")
 */
class Song
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $url;

    /**
     * @return bool
     */
    public function getOwn(): bool
    {
        return $this->own;
    }

    /**
     * @param bool $own
     */
    public function setOwn(bool $own): void
    {
        $this->own = $own;
    }


    private $own = false;
    /**
     * @var string
     *
     * @ORM\Column(type="text", name="original_url")
     */
    private $originalUrl;

    /**
     * @var string
     *
     * @ORM\Column(type="text", name="name")
     */
    private $songName;


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getOriginalUrl(): string
    {
        return $this->originalUrl;
    }

    /**
     * @param string $originalUrl
     */
    public function setOriginalUrl(string $originalUrl): void
    {
        $this->originalUrl = $originalUrl;
    }

    /**
     * @return string
     */
    public function getSongName(): string
    {
        return $this->songName;
    }

    /**
     * @param string $songName
     */
    public function setSongName(string $songName): void
    {
        $this->songName = $songName;
    }


}
