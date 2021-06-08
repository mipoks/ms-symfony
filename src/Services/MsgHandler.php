<?php


namespace App\Services;


use Stomp\Transport\Frame;
use App\Entity\Chat;
use App\Entity\ChatMessage;
use App\Entity\Person;
use App\Entity\Song;
use App\Repository\MessageRepository;
use App\Repository\PersonRepository;
use App\Repository\SongRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MsgHandler
{
    public function __invoke(Frame $message) {

        echo ("<script>console.log('from msghandler: wow работает');</script>");
        if ($message->body === '...') {
            return true;
        }
        return false;
    }
}