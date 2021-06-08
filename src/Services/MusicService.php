<?php


namespace App\Services;


use App\Entity\Person;
use App\Entity\Song;
use App\Repository\PersonRepository;
use App\Repository\SongRepository;
use App\Util\MusicGetter;
use App\Util\SongOwnChecker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MusicService
{
    private $songRepository;
    private $songOwnChecker;
    private $personRepository;
    private $musicGetter;

    public function __construct(SongRepository $songRepository, SongOwnChecker $songOwnChecker,
                                PersonRepository $personRepository, MusicGetter $musicGetter)
    {
        $this->songRepository = $songRepository;
        $this->songOwnChecker = $songOwnChecker;
        $this->personRepository = $personRepository;
        $this->musicGetter = $musicGetter;
    }

    public function getMusicById(int $id): ?Song
    {
        return $this->songRepository->findById($id);
    }

    public function getRandomMusic(): ?Song
    {
        return $this->songRepository->findOneByRandom();
    }

    public function search(string $search, ?Person $person): ?array
    {
        $count = 5;

        if ($person != null) {
            $person = $this->personRepository->findByEmail($person->getEmail());
            $count = $person->getSongCount();
        }

        $songs = null;
        if ($search == "")
            $songs = $this->musicGetter->get($count);
        else
            $songs = $this->musicGetter->getWithSearch($count, $search);
        $this->songRepository->saveAll($songs);
        if ($person != null) {
            $songs = $this->songOwnChecker->addParamIfInSongRepo($songs, $person->getSongs()->toArray());
            echo ("<script>console.log('from musicgetter: ".json_encode($songs)."');</script>");
        }
        return $songs;
    }

    public function getPopularSongs(?Person $person): ?array
    {
        return $this->search("", $person);
    }
}
