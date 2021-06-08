<?php

namespace App\Repository;


use App\Entity\Chat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;
use function Symfony\Component\String\u;


class ChatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chat::class);
    }

    /**
     * @param int $personId
     * @return Chat[]
     */
    public function findByPersonId(int $personId): array
    {
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult(Chat::class, 'd');
        $rsm->addFieldResult('d', 'id', 'id');
        $rsm->addFieldResult('d', 'is_open', 'isOpen');

        $query = $this->getEntityManager()->createNativeQuery('SELECT * FROM chat_person LEFT JOIN chats ON chats.id = chat_person.chat_id WHERE chat_person.person_id ='.$personId, $rsm);
        return $query->getResult();
    }


    /**
     * @param string $id
     * @return object
     */
    public function findById(string $id): ?Chat
    {
        return $this->findOneBy(
            array('id' => $id)
        );
    }

    public function save(Chat $chat) : ?Chat
    {
        try {
            $this->_em->persist($chat);
            $this->_em->flush();
            return $chat;
        } catch (ORMException $e) {
            echo $e->getTraceAsString();
        }
        return null;
    }

}
