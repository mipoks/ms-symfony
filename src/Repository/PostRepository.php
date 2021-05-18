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
    public function __construct(ManagerRegistry $registry)
    {
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
        return $this->createQueryBuilder('p')
            ->andWhere('p.person_id = :person_id')
            ->setParameter('person_id', $personId)
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?Post
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
