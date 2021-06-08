<?php

namespace App\Repository;


use App\Entity\Chat;
use App\Entity\ChatMessage;
use App\Entity\Song;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;
use function Symfony\Component\String\u;


class SongRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Song::class);
    }

    /**
     * @return Song
     */
    public function findOneByRandom(): ?Song
    {
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult(Song::class, 'd');
        $rsm->addFieldResult('d', 'id', 'id');
        $rsm->addFieldResult('d', 'original_url', 'originalUrl');
        $rsm->addFieldResult('d','name','songName');
        $rsm->addFieldResult('d','url','url');

        $query = $this->getEntityManager()->createNativeQuery('SELECT * FROM songs ORDER BY random() LIMIT 1', $rsm);
        $tt =  $query->getResult();
        if (reset($tt) == false)
            return null;
        else {
            return reset($tt);
        }
    }

    public function saveAll(array $songs): void
    {
        foreach ($songs as $song) {
            $this->save($song);
        }
    }

    public function save(Song $song)
    {
        $songFromDb = $this->findById($song->getId());
        if ($songFromDb == null)
            $songFromDb = $song;
        else {
            $songFromDb->setSongName($song->getSongName());
            $songFromDb->setOriginalUrl($song->getOriginalUrl());
            $songFromDb->setUrl($song->getUrl());
            $songFromDb->setOwn($song->getOwn());
        }
        try {
            $this->_em->persist($songFromDb);
            $this->_em->flush();
        } catch (ORMException $e) {
            echo $e->getTraceAsString();
        }
    }

    /**
     * @param string $id
     * @return Song|null
     */
    public function findById(string $id): ?Song
    {
        return $this->findOneBy(
            array('id' => $id)
        );
    }

}
