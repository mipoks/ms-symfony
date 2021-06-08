<?php

namespace App\Repository;


use App\Entity\PersonInfo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use function Symfony\Component\String\u;


class PersonInfoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonInfo::class);
    }

    /**
     * @param int $personId
     * @return object
     */
    public function findByPersonId(int $personId): ?PersonInfo
    {
        return $this->findOneBy(
            array('person_id' => $personId)
        );
    }

    public function save(PersonInfo $personInfo) : ?PersonInfo
    {
        try {
            $this->_em->persist($personInfo);
            $this->_em->flush();
            return $personInfo;
        } catch (ORMException $e) {
            echo $e->getTraceAsString();
        }
        return null;
    }

}
