<?php

namespace App\Repository;

use App\Entity\News;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method News|null find($id, $lockMode = null, $lockVersion = null)
 * @method News|null findOneBy(array $criteria, array $orderBy = null)
 * @method News[]    findAll()
 * @method News[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, News::class);
    }

    public function getPaginateSort($page, $elementCount)
    {

        return  $this->getEntityManager()->
        createQuery(
            'SELECT n
            FROM App\Entity\News n
            WHERE n.publishedAt < current_date() AND n.isActive = true AND n.isHide = false
            ORDER BY n.publishedAt DESC'
        )->setFirstResult(($page-1)*$elementCount)
            ->setMaxResults($elementCount)->getResult();

    }

    public function getSort()
    {
        return $this->getEntityManager()->
        createQuery(
            'SELECT n
            FROM App\Entity\News n
            WHERE n.publishedAt < current_date() AND n.isActive = true AND n.isHide = false
            ORDER BY n.publishedAt DESC'
        )->getResult();
    }

    public function getSlug($slug)
    {
        return $this->getEntityManager()->
        createQuery(
            'SELECT n
            FROM App\Entity\News n
            WHERE n.slug = :slug AND n.publishedAt < current_date() AND n.isActive = true
            '
        )->setParameter('slug', $slug)->getResult();
    }


}
