<?php

namespace App\Controller;

use App\Services\MusicService;
use App\Services\PersonGiver;
use App\Services\PersonMusicService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/me")
 */
class MyMusicController extends AbstractController
{
    /**
     * @Route(methods="GET", name="me")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request, PersonGiver $personGiver, PersonMusicService $musicService): Response
    {
        $person = $personGiver->get();
        $songs = $musicService->getMusic($person);
        return $this->render('audio-page.html.twig', [
            'songemptytext' => "Вы ещё ничего не добавили",
            'songs' => $songs,
            'title' => "Сохраненное",
            'user' => $person
        ]);
    }


    /**
     * @Route("/{id}", methods="POST", name="me_post")
     * @param Request $request
     * @return Response
     */
    public function putSong($id, PersonGiver $personGiver, PersonMusicService $musicService): Response
    {
        $person = $personGiver->get();
        $response = new Response();
        if ($musicService->toggleMusicById($id, $person)) {
            $response->setStatusCode(202);
        } else {
            $response->setStatusCode(203);
        }
        return $response;
    }

    /**
     * @Route("/{id}", methods="DELETE", name="me_delete")
     * @param Request $request
     * @return Response
     */
    public function deleteSong($id, PersonGiver $personGiver, PersonMusicService $musicService) {
        $person = $personGiver->get();
        $musicService->deleteMusic($id, $person);
        return new Response(null, 200);
    }

}
