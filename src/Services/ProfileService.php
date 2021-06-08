<?php


namespace App\Services;


use App\Entity\Person;
use App\Entity\Song;
use App\Form\PersonForm;
use App\Repository\PersonRepository;
use App\Repository\SongRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProfileService
{
    public static $SUCCESS = 0;
    public static $UNKNOWN_ERROR = -2;
    public static $NOT_UPDATED = -1;
    public static $INCORRECT_PWD = -3;

    private $dbPerson;
    private $passwordEncoder;

    public function update(PersonForm $personForm, Person $person): ?int
    {
        if ($personForm->getSongCount() > 0) {
            $person->setSongCount($personForm->getSongCount());
        }
        if ($personForm->getPassword() != null && $personForm->getPwd2() != null) {
            if ($this->passwordEncoder->isPasswordValid($person, $personForm->getPassword())) {
                $person->setPassword($this->passwordEncoder->encodePassword($person, $personForm->getPwd2()));
            } else {
                return ProfileService::$INCORRECT_PWD;
            }
        }

        if ($personForm->getName() != null) {
            $person->setName($personForm->getName());
        }

        return $this->dbPerson->save($person)->getId();
    }

    public function __construct(PersonRepository $dbPerson, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->dbPerson = $dbPerson;
        $this->passwordEncoder = $passwordEncoder;
    }

}