<?php

namespace App\Controller;

use App\Entity\Chat;
use App\Entity\ChatMessage;
use App\Form\ChatForm;
use App\Repository\PersonRepository;
use App\Services\ChatService;
use App\Services\PersonGiver;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChatController extends AbstractController
{


    /**
     * @Route("/chats", methods="GET", name="chats")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request, ChatService $chatService, PersonGiver $personGiver): Response
    {
        return $this->render('chat.html.twig', [
            'title' => 'Мессенджер'
        ]);
    }


    /**
     * @Route("/chat/{id}", methods="GET", name="chat")
     * @param Request $request
     * @param string $id
     * @param ChatService $chatService
     * @return Response
     */
    public function getChat(Request $request, string $id, ChatService $chatService, PersonRepository $personRepository)
    {
        $chatId = $chatService->getRealChatId($id);
        $smallChatId = $chatService->getSmallChatId($chatId);
        $messageList = $chatService->getMessages($chatId);

        $person = $personRepository->findById((int) $smallChatId);
        return $this->render('chat-with.html.twig', [
            "chatId" => $chatId,
            "messages" => $messageList,
            "recipientId" => $smallChatId,
            "title" => "Чат с пользователем",
            'person' => $person
        ]);
    }

    /**
     * @Route("/chat", methods="POST", name="chatSend")
     * @param Request $request
     * @param ChatService $chatService
     * @param PersonGiver $personGiver
     */
    public function sendMessage(Request $request, ChatService $chatService, PersonGiver $personGiver) : Response
    {
        $request = $this->transformJsonBody($request);
        $person = $personGiver->get();
        $chatMessage = new ChatMessage();
        try {
            echo ("<script>console.log('from chatcontroller: norm');</script>");

            $chatMessage->setChatId($request->get('chatId'));
            $chatMessage->setContent($request->get('content'));
            $chatMessage->setRecipientId($request->get('recipientId'));
            $chatMessage->setSenderId($request->get('senderId'));

            $chatService->sendMessage($chatMessage, $person);
        } catch (\Exception $ex) {
            echo ("<script>console.log('from chatcontroller: ".$ex->getTraceAsString()."');</script>");
        }
        return new Response();
    }

    protected function transformJsonBody(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return $request;
        }

        $request->request->replace($data);

        return $request;
    }


    /**
     * @Route("/interlocutors", methods="GET", name="interChat")
     * @param Request $request
     * @return Response
     */
    public function getAllChats(PersonGiver $personGiver, ChatService $chatService)
    {
        $person = $personGiver->get();
        $chats = $chatService->getAllChats($person);
        $chatForms = array();
        foreach ($chats as $chat) {
            $tempChatForm = new ChatForm($chat->getId(), $chat->generateName());
            array_push($chatForms, $tempChatForm);
        }
        $serializedEntity = $this->container->get('serializer')->serialize($chatForms, 'json');
        return new Response($serializedEntity);
    }
}
