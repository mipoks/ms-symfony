<?php


namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PersonInfoRepository")
 * @ORM\Table(name="person_info")
 */
class PersonInfo
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

//    /**
//     * @OneToOne(targetEntity="Person", inversedBy="person_info", fetch="EAGER")
//     * @JoinColumn(name="person_id", referencedColumnName="id")
//     */
//    private $person;

    /**
     * @var int
     *
     * @ORM\Column(type="bigint")
     */
    private $personId;


//    /**
//     * @ManyToMany(targetEntity="Person", fetch="EAGER")
//     * @JoinTable(name="person_info_friends",
//     *      joinColumns={@JoinColumn(name="person_info_id", referencedColumnName="id")},
//     *      inverseJoinColumns={@JoinColumn(name="person_id", referencedColumnName="id", unique=true)}
//     *      )
//     */
    private $friends = array();

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
     * @return mixed
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @param mixed $person
     */
    public function setPerson($person): void
    {
        $this->person = $person;
    }

    /**
     * @return array
     */
    public function getFriends(): array
    {
        return $this->friends;
    }

    /**
     * @param array $friends
     */
    public function setFriends(array $friends): void
    {
        $this->friends = $friends;
    }


}
