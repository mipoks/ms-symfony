<?php

namespace App\Repository;


use App\Entity\Chat;
use App\Entity\ChatMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;


class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChatMessage::class);
    }

    /**
     * @param string $chatId
     * @return ChatMessage[]
     */
    public function findByChatId(string $chatId): ?array
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT msg FROM App\Entity\ChatMessage msg WHERE msg.chatId = :chatId'
            )->setParameter('chatId', $chatId)
            ->getResult();
    }

    public function save(ChatMessage $chatMessage) : ?ChatMessage
    {
        try {
            $this->_em->persist($chatMessage);
            $this->_em->flush();
            return $chatMessage;
        } catch (ORMException $e) {
            echo $e->getTraceAsString();
        }
        return null;
    }

}
