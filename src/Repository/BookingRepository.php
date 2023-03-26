<?php

namespace App\Repository;

use App\Entity\Booking;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Booking>
 *
 * @method Booking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Booking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Booking[]    findAll()
 * @method Booking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function book(Booking $entity, int $maxVacancies): bool
    {
        $count = $this->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->andWhere('b.date_from <= :dateTo')
            ->andWhere('b.date_to >= :dateFrom')
            ->setParameter('dateTo', $entity->getDateTo())
            ->setParameter('dateFrom', $entity->getDateFrom())
            ->setMaxResults($maxVacancies)
            ->getQuery()
            ->getSingleScalarResult();

        if ($count < $maxVacancies) {
            $this->save($entity, true);
            return true;
        }

        return false;
    }

    public function list(): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.date_from > :now')
            ->setParameter('now', new DateTime())
            ->orderBy('b.date_from')
            ->getQuery()
            ->getResult();
    }

    public function save(Booking $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Booking $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
