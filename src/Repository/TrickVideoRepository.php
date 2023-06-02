<?php

namespace App\Repository;

use App\Entity\TrickVideo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TrickVideo>
 *
 * @method TrickVideo|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrickVideo|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrickVideo[]    findAll()
 * @method TrickVideo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrickVideoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrickVideo::class);
    }

    public function save(TrickVideo $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TrickVideo $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


}
