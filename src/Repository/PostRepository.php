<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    private $personRepository;

    public function __construct(ManagerRegistry $registry, PersonRepository $personRepository)
    {
        $this->personRepository = $personRepository;
        parent::__construct($registry, Post::class);
    }

    public function save(Post $post)
    {
        try {
            $this->_em->persist($post);
            $this->_em->flush();
        } catch (ORMException $e) {
            echo $e->getTraceAsString();
        }
    }

    public function delete(Post $post) {
        try {
            $this->getEntityManager()->remove($post);
        } catch (ORMException $e) {
        }
    }

    /**
     * @param int $personId
     * @return Post[] Returns an array of Post objects
     */
    public function findByPersonId(int $personId): ?array
    {
        $optional = $this->personRepository->findById($personId);
        if ($optional == null)
            return null;
        return $this->createQueryBuilder('post')
            ->andWhere('post.person = :person')
            ->setParameter('person', $optional)
            ->orderBy('post.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param string $id
     * @return object
     */
    public function findById(string $id): ?object
    {
        return $this->findOneBy(
            array('id' => $id)
        );
    }
}
