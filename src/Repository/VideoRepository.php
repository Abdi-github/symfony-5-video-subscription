<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Video;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function Symfony\Component\DependencyInjection\Loader\Configurator\expr;
use Doctrine\ORM\Query\Expr;

/**
 * @method Video|null find($id, $lockMode = null, $lockVersion = null)
 * @method Video|null findOneBy(array $criteria, array $orderBy = null)
 * @method Video[]    findAll()
 * @method Video[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Video::class);
    }

    public function newVideosByLimit(int $limit)
    {
        $qb = $this->createQueryBuilder('v');
        $qb->select('v')
            ->orderBy('v.created_at', 'DESC')
            ->setMaxResults($limit);



        return $qb->getQuery()->getResult();
    }

    public function getPaginatedVideos($page,  $limit, $category_id, $sort_criteria, $sort_method)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('c.id = ' . $category_id)
            ->leftJoin('v.category', 'c')
            ->setFirstResult(($page * $limit) - $limit)
            ->orderBy('v.' . $sort_criteria, $sort_method)
            ->setMaxResults($limit)
            ->getQuery()
            ->execute();
    }

    // public function getPaginatedVideos($page,  $limit, $category_id, $sort_criteria, $sort_method)
    // {
    //     $qb = $this->createQueryBuilder('v');

    //     $qb
    //         // ->innerJoin(
    //         //     Category::class,    // Entity
    //         //     'c',               // Alias
    //         //     Expr\Join::WITH,        // Join type
    //         //     'c.id = v.category' // Join columns
    //         // )
    //         // ->innerJoin('v.category', 'c', 'WITH', 'c = v.category')
    //         // ->innerJoin('p.user', 'u', Join::WITH, 'p.user = u')
    //         // ->innerJoin('v.category', 'c', Join::WITH, 'v.category = c')
    //         // ->select('v')

    //         ->where('c.id = ' . $category_id)
    //         ->innerJoin('App\Entity\Category', 'c', \Doctrine\ORM\Query\Expr\Join::WITH, 'c = v.category')


    //         // ->setParameter('category', $category_id)
    //         // \dd($qb->getQuery()->getResult());

    //         // ->innerJoin('App\Entity\Category', 'c', Expr\Join::WITH, 'c = v.category')
    //         // // ->select('c.id');
    //         // ->where(
    //         //     $qb->expr()->andX(
    //         //         $qb->expr()->eq('c.id', $category_id)
    //         //     )

    //         // )
    //         ->setFirstResult(($page * $limit) - $limit)
    //         ->orderBy('v.' . $sort_criteria, $sort_method)
    //         ->setMaxResults($limit);



    //     // dd($qb->getQuery()->getResult());
    //     return $qb->getQuery()->getResult();
    // }

    public function getCategorizedTotalVideos($category_id)
    {
        $qb = $this->createQueryBuilder('v');

        $qb
            // ->innerJoin('p.category', 'c', 'WITH', 'c = p.category')

            // ->innerJoin('App\Entity\Category', 'c', Expr\Join::WITH, 'c = p.category')

            // ->innerJoin(
            //     Category::class,    // Entity
            //     'c',               // Alias
            //     Expr\Join::WITH,        // Join type
            //     'c.id = p.category' // Join columns
            // )

            ->select($qb->expr()->count('v.id'))
            ->where('c.id = ' . $category_id)
            ->innerJoin('App\Entity\Category', 'c', \Doctrine\ORM\Query\Expr\Join::WITH, 'c = v.category')

            // ->where(
            //     $qb->expr()->andX(
            //         $qb->expr()->eq('c.id', $category_id)
            //     )
            // )
            ->orderBy('v.created_at', 'DESC');


        // dd($qb->getQuery()->getResult());
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findVideosByName(string $query)
    {

        $querybuilder = $this->createQueryBuilder('v');
        $searchTerms = $this->prepareQuery($query);

        foreach ($searchTerms as $key => $term) {
            $querybuilder
                ->orWhere('v.name LIKE :t_' . $key)

                ->setParameter('t_' . $key, '%' . trim($term) . '%');
        }


        return $querybuilder->getQuery()->getResult();
    }
    public function totalSearchResult(string $query,  $page, $limit)
    {

        $querybuilder = $this->createQueryBuilder('v');
        $searchTerms = $this->prepareQuery($query);

        foreach ($searchTerms as $key => $term) {
            $querybuilder
                ->select($querybuilder->expr()->count('v.id'))
                ->orWhere('v.name LIKE :t_' . $key)
                ->setFirstResult(($page * $limit) - $limit)
                ->setMaxResults($limit)
                ->setParameter('t_' . $key, '%' . trim($term) . '%');
        }


        return $querybuilder->getQuery()->getSingleScalarResult();
    }

    private function prepareQuery(string $query): array
    {
        return explode(' ', $query);
    }



    // /**
    //  * @return Video[] Returns an array of Video objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Video
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
