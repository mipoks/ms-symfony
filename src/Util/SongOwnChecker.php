<?php


namespace App\Util;


use App\Entity\Person;
use App\Entity\Song;
use App\Repository\PersonRepository;
use App\Repository\SongRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SongOwnChecker
{

    public function addParamIfInSongRepo(array $toFind, array $from): ?array
    {
        foreach ($toFind as $song) {
            $song->setOwn($this->contains($song, $from));
        }
        return $toFind;
    }

    public function addParamIfOwn(array $collection, bool $own): array
    {
        foreach ($collection as $song) {
            $song->setOwn($own);
        }
        return $collection;
    }

    public
    function contains($song, array $from): bool
    {
        foreach ($from as $songIn) {
            if ($songIn == $song)
                return true;
        }
        return false;
    }

}