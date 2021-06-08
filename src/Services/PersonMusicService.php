<?php


namespace App\Services;


use App\Entity\Person;
use App\Entity\Song;
use App\Form\SongForm;
use App\Repository\PersonRepository;
use App\Repository\SongRepository;
use App\Util\MusicGetter;
use App\Util\SongOwnChecker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PersonMusicService
{

    private $musicGetter;
    private $songOwnChecker;
    private $personRepository;
    private $songRepository;

    public function __construct(SongRepository $songRepository, SongOwnChecker $songOwnChecker,
                                PersonRepository $personRepository, MusicGetter $musicGetter)
    {
        $this->songRepository = $songRepository;
        $this->songOwnChecker = $songOwnChecker;
        $this->personRepository = $personRepository;
        $this->musicGetter = $musicGetter;
    }


    public function getMusic(Person $person): array
    {
        $person = $this->personRepository->findByEmail($person->getEmail());
        $songs = $person->getSongs()->toArray();
        echo ("<script>console.log('from PersonMusicService: ".json_encode((array)$songs[0])."');</script>");
        $songs = $this->songOwnChecker->addParamIfOwn($songs, true);
        echo ("<script>console.log('from PersonMusicService: ".json_encode((array)$songs[0])."');</script>");
        $this->songRepository->saveAll($songs);

        echo ("<script>console.log('from PersonMusicService: ".json_encode($songs)."');</script>");
        return $songs;
    }

    public function toggleMusic(SongForm $songForm, Person $person): bool
    {
        return $this->toggleMusicById($songForm->getId(), $person);
    }

    public function toggleMusicById(int $id, Person $person): bool
    {
        $optional = $this->songRepository->findById($id);
        $person = $this->personRepository->findByEmail($person->getEmail());
        if ($optional != null) {
            $ans = false;
            if ($person->hasSong($optional)) {
                $ans = $person->removeSong($optional);
            } else {
                $ans = $person->addSong($optional);
            }
            $this->personRepository->save($person);
            return $ans;
        }
        return false;
    }

    public function deleteMusic(int $id, Person $person): bool
    {
        $optional = $this->songRepository->findById($id);
        $person = $this->personRepository->findByEmail($person->getEmail());
        if ($optional !=null) {
            $temp = $optional;
            $ans = $person->removeSong($temp);
            return $ans;
        }

        return false;
    }
}