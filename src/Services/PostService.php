<?php


namespace App\Services;


use App\Entity\Chat;
use App\Entity\ChatMessage;
use App\Entity\Person;
use App\Entity\Post;
use App\Entity\Song;
use App\Repository\ChatRepository;
use App\Repository\MessageRepository;
use App\Repository\PersonRepository;
use App\Repository\PostRepository;
use App\Repository\SongRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PostService
{

    private $postRepository;
    private $personRepository;

    public function __construct(PostRepository $postRepository,
                                PersonRepository $personRepository)
    {
        $this->postRepository = $postRepository;
        $this->personRepository = $personRepository;
    }

    public function getPosts(int $personId): ?array
    {
        return $this->postRepository->findByPersonId($personId);
    }

    public function addPost(Post $post)
    {
        $this->postRepository->save($post);
    }

    public function deletePostById(int $postId, Person $person)
    {
        $post = $this->postRepository->findById($postId);
        if ($post != null) {
            if ($post->getPerson() === $person) {
                $this->postRepository->delete($post);
            }
        }
    }

}