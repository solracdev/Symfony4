<?php

namespace App\Repository;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findAll()
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationRepository extends ServiceEntityRepository {

    public function __construct(RegistryInterface $registry) {
        parent::__construct($registry, Notification::class);
    }

    public function findUnseenByUser(User $user) {

        $qb = $this->createQueryBuilder("n");

        return $qb->select("count(n)")
                        ->where("n.user = :user")
                        ->andWhere("n.seen = 0")
                        ->setParameter("user", $user)
                        ->getQuery()
                        ->getSingleScalarResult();
    }
    
    public function markAllAsReadByUser(User $user){
        
        // Instancia del queryBuilder
        $qb = $this->createQueryBuilder("n");
        
        // En este caso no es un select, haremos un update
        $qb->update("App\Entity\Notification","n")
                ->set("n.seen", true)
                ->where("n.user = :user")
                ->setParameter("user", $user)
                ->getQuery()
                ->execute(); // aqui ejecutamos directamente la query para uqe se apliquen los cambios
        
    }

//    /**
//     * @return Notification[] Returns an array of Notification objects
//     */
    /*
      public function findByExampleField($value)
      {
      return $this->createQueryBuilder('n')
      ->andWhere('n.exampleField = :val')
      ->setParameter('val', $value)
      ->orderBy('n.id', 'ASC')
      ->setMaxResults(10)
      ->getQuery()
      ->getResult()
      ;
      }
     */

    /*
      public function findOneBySomeField($value): ?Notification
      {
      return $this->createQueryBuilder('n')
      ->andWhere('n.exampleField = :val')
      ->setParameter('val', $value)
      ->getQuery()
      ->getOneOrNullResult()
      ;
      }
     */
}
