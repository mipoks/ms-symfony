<?php


namespace App\Services;


use App\Entity\Chat;
use App\Entity\ChatMessage;
use App\Entity\Person;
use App\Entity\Song;
use App\Repository\MessageRepository;
use App\Repository\PersonRepository;
use App\Repository\SongRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ChatMessageService
{

    private $messageRepository;

    public function __construct(MessageRepository $messageRepository)
    {
        $this->messageRepository = $messageRepository;
    }

    public function save(ChatMessage $chatMessage): ChatMessage
    {
        return $this->messageRepository->save($chatMessage);
    }

    public function getMessages(Chat $chat): ?array
    {
        return $this->messageRepository->findByChatId($chat->getId());
    }
}