<?php


namespace App\Controller;

use App\Entity\Chat;
use App\Entity\ChatMessage;
use App\Entity\Post;
use App\Form\ChatForm;
use App\Repository\PersonRepository;
use App\Repository\SongRepository;
use App\Services\ChatService;
use App\Services\PersonGiver;
use App\Services\PersonService;
use App\Services\PostService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserPageController extends AbstractController
{


    /**
     * @Route("/users", methods="GET", name="get_users")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request, PersonService $infoService, PersonGiver $personGiver): Response
    {
        $persons = $infoService->getAllPerson();
        return $this->render('users.html.twig', [
            'users' => $persons,
            'title' => 'Поиск пользователя'
        ]);
    }

    /**
     * @Route("/user/{id}", methods="GET", name="user")
     * @param Request $request
     * @return Response
     */
    public function userPage($id, PersonGiver $personGiver, Request $request, PersonService $infoService, PostService $postService, PersonRepository $personRepository): Response
    {
        $authed = $personGiver->get();
        $person = $personRepository->findById($id);
        $posts = $postService->getPosts($id);
        $params = [
            'person' => $person,
            'posts' => $posts,
            'title' => 'Стена пользователя',
            'postemptytext' => 'Здесь пока ничего нет'
        ];
        if ($authed != null and $id == $authed->getId()) {
            $params['own'] = true;
            $params['postemptytext'] = "Вы пока ничего не опубликовали";
        }
        return $this->render('wall-page.html.twig', $params);
    }


    /**
     * @Route("/post/add", methods="POST", name="add_post")
     * @param Request $request
     * @return Response
     */
    public function postAdd(Request $request, PersonService $infoService, SongRepository $songRepository, PostService $postService, PersonGiver $personGiver): Response
    {
        $post = new Post();
        $postText = $request->request->get('text');
        $songs = $request->request->get('songs');
        if ($songs == null && ($postText == null or $postText == "")) {
            return new Response("Empty post", 400);
        }
        if ($songs != null) {
            foreach ($songs as $songId) {
                if ($songId != null) {
                    $optionalSong = $songRepository->findById($songId);
                    $post->addSong($optionalSong);
                }
            }
        }
        $post->setText($postText);
        $post->setPerson($personGiver->get());
        $postService->addPost($post);
        $answer = ['result' => 'Успешно!', 'text' => 'Вы можете посмотреть свою стену'];
        return new Response(json_encode($answer), 200);
    }

    /**
     * @Route("/post/{id}", methods="DELETE", name="delete_post")
     * @param Request $request
     * @return Response
     */
    public function postDelete($id, Request $request, PersonService $infoService, PostService $postService, PersonGiver $personGiver): Response
    {
        $postService->deletePostById($id, $personGiver->get());
        $response = new Response();
        $response->setStatusCode(202);
        return $response;
    }

}
