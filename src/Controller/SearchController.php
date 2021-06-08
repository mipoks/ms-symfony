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
 * @Route("/", "/search")
 */
class SearchController extends AbstractController
{
    /**
     * @Route(methods="GET", name="search")
     * @param Request $request
     * @param MusicService $searchService
     * @param PersonGiver $personGiver
     * @return Response
     */
    public function index(Request $request, MusicService $searchService, PersonGiver $personGiver): Response
    {
        $person = $personGiver->get();
        $search = $request->query->get('search');
        $music = null;
        if ($search == null) {
            $music = $searchService->getRandomMusic();

            $map = [
                'title' => "Поиск",
                'user' => $person,
            ];

            if ($music != null) {
                $map['somemusic'] = $music->getSongName();
            }
            return $this->render('search.html.twig', $map);
        } else {
            $songs = $searchService->search($search, $person);
            return $this->render('audio-page.html.twig', [
                'songemptytext' => "Ничего не нашлось",
                'songs' => $songs,
                'title' => "Поиск музыки"
            ]);
        }
    }
}
