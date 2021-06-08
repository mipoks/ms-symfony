<?php

namespace App\Controller;

use App\Entity\Chat;
use App\Entity\ChatMessage;
use App\Form\ChatForm;
use App\Form\SongText;
use App\Repository\SongRepository;
use App\Services\ChatService;
use App\Services\PersonGiver;
use App\Services\SongTextService;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SongTextController extends AbstractController
{

    /**
     * @Route("/song/text/{id}", methods="GET", name="get_song_text")
     * @param int $id
     * @param SongRepository $songRepository
     * @param SongTextService $songTextService
     * @return Response
     */
    public function getSongText(int $id, SongRepository $songRepository, SongTextService $songTextService): Response
    {
        $songText = null;
        $optionalSong = $songRepository->findById($id);
        if ($optionalSong != null) {
            $song = $optionalSong;
            try {
                $exploded = explode("–", $song->getSongName());
                $songName = $exploded[0];
                $artistName = $exploded[1];
                $songText = $songTextService->getText($songName, $artistName);
            } catch (Exception $ex) {
                $songName = explode("-", $song->getSongName())[0];
                $songText = $songTextService->getText($songName, null);
            }
        } else {
            return $this->response(null, 404);
        }
        return new Response($songText->toJson());
    }

}
