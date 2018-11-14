<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository {

    public function __construct(RegistryInterface $registry) {
        parent::__construct($registry, User::class);
    }

    public function findUsersToFollowExceptUser(User $user) {
        
        return $this->usersToFollowQueryBuilder()
                ->andHaving("u != :user")
                ->setParameter("user", $user)
                ->getQuery()
                ->getResult();
    }

    /**
     * Function que devuelve una queryBuilder con usuarios con 5 o mas post
     * @return QueryBuilder
     */
    private function usersToFollowQueryBuilder(): QueryBuilder {

        $qb = $this->createQueryBuilder("u");
        
        return $qb->select("u")
                ->innerJoin("u.posts", "mp")
                ->groupBy("u")
                ->having("count(mp) >= 5");
    }
    
    
    
    
    // SAMPLES

//    /**
//     * @return User[] Returns an array of User objects
//     */
    /*
      public function findByExampleField($value)
      {
      return $this->createQueryBuilder('u')
      ->andWhere('u.exampleField = :val')
      ->setParameter('val', $value)
      ->orderBy('u.id', 'ASC')
      ->setMaxResults(10)
      ->getQuery()
      ->getResult()
      ;
      }
     */

    /*
      public function findOneBySomeField($value): ?User
      {
      return $this->createQueryBuilder('u')
      ->andWhere('u.exampleField = :val')
      ->setParameter('val', $value)
      ->getQuery()
      ->getOneOrNullResult()
      ;
      }
     */
}
