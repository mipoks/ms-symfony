<?php


namespace App\Services;


use App\Entity\Chat;
use App\Entity\ChatMessage;
use App\Entity\Person;
use App\Entity\Song;
use App\Repository\ChatRepository;
use App\Repository\MessageRepository;
use App\Repository\PersonRepository;
use App\Repository\SongRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ChatService
{

    private $chatRepository;
    private $messagingTemplate;
    private $chatMessageService;
    private $messageRepository;
    private $personRepository;
    private $personGiver;

    public function __construct(ChatRepository $chatRepository, ChatMessageService $chatMessageService,
                                PersonRepository $personRepository, MessageRepository $messageRepository,
                                PersonGiver $personGiver)
    {
        $this->chatRepository = $chatRepository;
        $this->chatMessageService = $chatMessageService;
        $this->personRepository = $personRepository;
        $this->messageRepository = $messageRepository;
        $this->personGiver = $personGiver;
    }

    public function getMessages(string $chatId): ?array
    {
        $chat = $this->chatRepository->findById($chatId);
        if ($chat != null) {
            return $this->messageRepository->findByChatId($chatId);
        } else {
            return null;
        }
    }

    public function getAllChats(Person $person): array
    {
        return $this->chatRepository->findByPersonId($person->getId());
    }

    public function sendMessage(ChatMessage $chatMessage, Person $person): void
    {
        if ($person->getId() != $chatMessage->getSenderId()) {
            return;
        }

        $chatId = $chatMessage->getChatId();
        $optionalChat = $this->chatRepository->findById($chatId);

        if ($optionalChat != null) {
            $chat = $optionalChat;
            if ($chat->getPersons()->contains($person)) {
                $chatMessage->setSenderName($person->getName());
                $msg = $this->chatMessageService->save($chatMessage);

                foreach ($chat->getPersons() as $p) {
//                    messagingTemplate . convertAndSendToUser(
//                        String . valueOf(p . getId()), "/queue/messages", msg);
                }
            }
        }
    }

    public function getOpenChat(): ?array
    {
        return null;
    }

    public function getSmallChatId(string $realId) : ?string
    {
        $smallId = null;
        $person = $this->personGiver->get();
        if (str_contains($realId, "_")) {
            $delId = (string)$person->getId();
            $smallId = str_replace([$delId, "_"], "" , $realId);
        }
        return $smallId;
    }
    public function getRealChatId(string $smallId)
    {
        $person = $this->personGiver->get();
        if (str_contains($smallId, "_")) {
            $delId = (string)$person->getId();
            $smallId = str_replace([$delId, "_"], "" , $smallId);
        }

        $chatId = $this->makeId((string)$person->getId(), $smallId);
        $chatOptional = $this->chatRepository->findById($chatId);
        $chat = null;
        if ($chatOptional == null) {
            $optionalPerson = $this->personRepository->findById((int)$smallId);
            if ($optionalPerson != null) {
                $chat = new Chat();
                $chat->setPersons([$person, $optionalPerson]);
                $chat->setId($chatId);
                $chat->setIsOpen(false);
                $chat = $this->chatRepository->save($chat);
            }
        } else {
            $chat = $chatOptional;
        }
        return $chat == null ? null : $chat->getId();
    }

    private function makeId(string $id1, string $id2): string
    {
        $chatId = "";
        if (strcmp($id1, $id2) > 0) {
            $chatId = $chatId . $id1 . "_" . $id2;
        } else {
            $chatId = $chatId . $id2 . "_" . $id1;
        }
        return $chatId;
    }
}