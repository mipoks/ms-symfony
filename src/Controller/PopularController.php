<?php


namespace App\Controller;

use App\Services\MusicService;
use App\Services\PersonGiver;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/actual")
 */
class PopularController extends AbstractController
{
    /**
     * @Route(methods="GET", name="actual")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request, PersonGiver $personGiver, MusicService $searchService): Response
    {
        $person = $personGiver->get();
        $songs = $searchService->getPopularSongs($person);
        return $this->render('audio-page.html.twig', [
            "user" => $person,
            "songs" => $songs,
            "title" => "Популярное",
            "songemptytext" => "Хьюстон, у нас проблемы, так как ничего не нашлось"
        ]);
    }
}
