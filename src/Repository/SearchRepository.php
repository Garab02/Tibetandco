<?php

namespace App\Repository;

use App\Entity\SearchAnnonce;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SearchAnnonce>
 *
 * @method SearchAnnonce|null find($id, $lockMode = null, $lockVersion = null)
 * @method SearchAnnonce|null findOneBy(array $criteria, array $orderBy = null)
 * @method SearchAnnonce[]    findAll()
 * @method SearchAnnonce[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SearchAnnonceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SearchAnnonce::class);
    }

    public function save(SearchAnnonce $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SearchAnnonce $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

   /**
    * @return SearchAnnonce[] Returns an array of SearchAnnonce objects
    */


    public function search($product = null, $categorie = null){
        $query = $this->createQueryBuilder('p');
        $query->where('p.active = 1');
        if($product != null){
            $query->andWhere('MATCH_AGAINST(a.title, a.content) AGAINST (:mots boolean)>0')
                ->setParameter('product', $product);
        }
        if($categorie != null){
            $query->leftJoin('p.categories', 'c');
            $query->andWhere('c.id = :id')
                ->setParameter('id', $categorie);
        }
        return $query->getQuery()->getResult();
    }
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SearchAnnonce
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
