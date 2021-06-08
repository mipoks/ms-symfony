<?php

namespace App\Repository;


use App\Entity\Person;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use function Symfony\Component\String\u;


class PersonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Person::class);
    }

    public function delete(Person $person) {
        try {
            $person = $this->findById($person->getId());
            $this->getEntityManager()->remove($person);
            $this->getEntityManager()->flush();
        } catch (ORMException $e) {
        }
    }

    /**
     * @param string $email
     * @return object
     */
    public function findByEmail(string $email): ?Person
    {
        return $this->findOneBy(
            array('email' => $email)
        );
    }

    /**
     * @param int $id
     * @return object
     */
    public function findById(int $id): ?Person
    {
        return $this->findOneBy(
            array('id' => $id)
        );
    }

    public function save(Person $person) : ?Person
    {
        try {
            $this->_em->persist($person);
            $this->_em->flush();
            return $person;
        } catch (ORMException $e) {
            echo $e->getTraceAsString();
        }
        return null;
    }

}
