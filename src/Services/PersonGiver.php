<?php


namespace App\Services;


use App\Entity\Person;
use App\Entity\Song;
use App\Repository\PersonRepository;
use App\Repository\SongRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

class PersonGiver
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function get(): ?\Symfony\Component\Security\Core\User\UserInterface
    {
        return $this->security->getUser();
    }
}