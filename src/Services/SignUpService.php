<?php


namespace App\Services;


use App\Entity\Person;
use App\Form\PersonForm;
use App\Repository\PersonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SignUpService
{
    private $personRepository;

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, PersonRepository $personRepository)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->personRepository = $personRepository;
    }
    public function signUp(PersonForm $personForm) : int {
        $person = $this->personRepository->findByEmail($personForm->getEmail());
        if ($person != null) {
            return Constants::ALREADY_EXIST;
        }
        $person = new Person($personForm->getName(), $personForm->getEmail());
        $person->setPassword($this->passwordEncoder->encodePassword($person, $personForm->getPassword()));

        $this->personRepository->save($person);
        return Constants::SUCCESS;
    }

    public function expel(Person $person) :void {
        $this->personRepository->delete($person);
    }

}