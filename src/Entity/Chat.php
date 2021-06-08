<?php


namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ChatRepository")
 * @ORM\Table(name="chats")
 */
class Chat
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $isOpen;

    /**
     * @var Person[]|Collection
     *
     * @ORM\ManyToMany(targetEntity="Person")
     * @ORM\JoinTable(name="chat_person",
     *      joinColumns={@ORM\JoinColumn(name="chat_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="person_id", referencedColumnName="id")}
     * )
     */
    private $persons;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return bool
     */
    public function isOpen(): bool
    {
        return $this->isOpen;
    }

    /**
     * @param bool $isOpen
     */
    public function setIsOpen(bool $isOpen): void
    {
        $this->isOpen = $isOpen;
    }

    /**
     * @return Person[]|Collection
     */
    public function getPersons()
    {
        return $this->persons;
    }

    /**
     * @param Person[]|Collection $persons
     */
    public function setPersons($persons): void
    {
        $this->persons = $persons;
    }


    public function generateName(): string
    {
        $name = "Chat between: ";
        foreach ($this->persons as $person) {
            $name = $name . $person->getName() . " and ";
        }
        return substr($name, 0, strlen($name) - 5);
    }


}
