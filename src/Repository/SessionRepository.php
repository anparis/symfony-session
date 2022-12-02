<?php

namespace App\Repository;

use App\Entity\Session;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Session>
 *
 * @method Session|null find($id, $lockMode = null, $lockVersion = null)
 * @method Session|null findOneBy(array $criteria, array $orderBy = null)
 * @method Session[]    findAll()
 * @method Session[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SessionRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Session::class);
  }

  public function save(Session $entity, bool $flush = false): void
  {
    $this->getEntityManager()->persist($entity);

    if ($flush) {
      $this->getEntityManager()->flush();
    }
  }

  public function remove(Session $entity, bool $flush = false): void
  {
    $this->getEntityManager()->remove($entity);

    if ($flush) {
      $this->getEntityManager()->flush();
    }
  }

  // Return sessions that are older than current date
  public function sessionsPast()
  {
    $now = new DateTime();

    $entityManager = $this->getEntityManager();

    $query = $entityManager->createQuery(
      'SELECT s
            FROM App\Entity\Session s
            WHERE s.date_fin < :date'
    )->setParameter('date', $now);

    return $query->getResult();
  }

  // Return sessions that are happening currently
  public function sessionsCurrent()
  {
    $now = new DateTime();

    $entityManager = $this->getEntityManager();

    $query = $entityManager->createQuery(
      'SELECT s
      FROM App\Entity\Session s
      WHERE s.date_debut <= :date AND s.date_fin >= :date'
    )->setParameter('date', $now);

    return $query->getResult();
  }

  // Return sessions that will happen
  public function sessionsFuture()
  {
    $now = new DateTime();

    $entityManager = $this->getEntityManager();

    $query = $entityManager->createQuery(
      'SELECT s
            FROM App\Entity\Session s
            WHERE s.date_debut > :date'
    )->setParameter('date', $now);

    return $query->getResult();
  }

  public function findNonRegistered($session_id)
  {
    $em = $this->getEntityManager();
    $sub = $em->createQueryBuilder();

    $qb = $sub;
    $qb->select('s')
       ->from('\App\Entity\Stagiaire','s')
       ->leftJoin('s.sessions','se')
       ->where('se.id = :id');

    $sub = $em->createQueryBuilder();
    $sub->select('st')
        ->from('\App\Entity\Stagiaire','st')
        ->where($sub->expr()->notIn('st.id',$qb->getDQL()))
        ->setParameter('id',$session_id)
        ->orderBy('st.nom');

    $query = $sub->getQuery();
    return $query->getResult();
  }

  public function findNonProgrammes($session_id)
  {
    $em = $this->getEntityManager();
    $sub = $em->createQueryBuilder();

    // $sub->select('p')
    //    ->from('\App\Entity\Programme','p')
    //    ->leftJoin('p.session','se')
    //    ->where('se.id not like :id')
    //    ->setParameter('id',$session_id);
    $qb = $sub;
    $qb->select('mo')
       ->from('\App\Entity\Module','mo')
       ->innerJoin('m.programmes','p')
       ->innerJoin('p.session','se')
       ->where('se.id = :id');
         
    $sub = $em->createQueryBuilder();
    $sub->select('m')
       ->from('\App\Entity\Module','m')
       ->where($sub->expr()->notIn('m.id',$qb->getDQL()))
       ->setParameter('id',$session_id);

    $query = $sub->getQuery();
    return $query->getResult();
  }
  //    /**
  //     * @return Session[] Returns an array of Session objects
  //     */
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

  //    public function findOneBySomeField($value): ?Session
  //    {
  //        return $this->createQueryBuilder('s')
  //            ->andWhere('s.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->getQuery()
  //            ->getOneOrNullResult()
  //        ;
  //    }
}
