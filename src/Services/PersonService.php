<?php


namespace App\Services;


use App\Entity\Person;
use App\Entity\PersonInfo;
use App\Entity\Song;
use App\Repository\PersonInfoRepository;
use App\Repository\PersonRepository;
use App\Repository\SongRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PersonService
{
    private $personInfoRepository;
    private $personGiver;
    private $personRepository;

    public function getPersonInfo(int $personId): PersonInfo
    {
        $person = $this->personGiver->get();
        $personInfoOptional = $this->personInfoRepository->findByPersonId($personId);
        if ($personInfoOptional == null) {
            $personInfo = new PersonInfo();
            $personInfo->setPerson($person);
            return $this->personInfoRepository->save($personInfo);
        } else {
            return $personInfoOptional;
        }
    }

    public function __construct(PersonGiver $peronGiver, PersonInfoRepository $personInfoRepository, PersonRepository $personRepository)
    {
        $this->personGiver = $peronGiver;
        $this->personInfoRepository = $personInfoRepository;
        $this->personRepository = $personRepository;
    }

    public function getAllPerson(): array
    {
        return $this->personRepository->findAll();
    }

}