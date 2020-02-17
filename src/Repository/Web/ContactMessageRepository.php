<?php

namespace App\Repository\Web;

use App\Entity\Web\ContactMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry as RegistryInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * Class ContactMessageRepository.
 *
 * @category Repository
 *
 * @author   Wils Iglesias <wiglesias83@gmail.com>
 */
class ContactMessageRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ContactMessage::class);
    }

    /**
     * @return QueryBuilder
     */
    public function getPendingMessagesAmountQB()
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.answered = :answered')
            ->setParameter('answered', false)
        ;
    }

    /**
     * @return Query
     */
    public function getPendingMessagesAmountQ()
    {
        return $this->getPendingMessagesAmountQB()->getQuery();
    }

    /**
     * @return int|array
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getPendingMessagesAmount()
    {
        return $this->getPendingMessagesAmountQ()->getSingleScalarResult();
    }

    /**
     * @return QueryBuilder
     */
    public function getReadPendingMessagesAmountQB()
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.checked = :checked')
            ->setParameter('checked', false)
        ;
    }

    /**
     * @return Query
     */
    public function getReadPendingMessagesAmountQ()
    {
        return $this->getReadPendingMessagesAmountQB()->getQuery();
    }

    /**
     * @return int|array
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getReadPendingMessagesAmount()
    {
        return $this->getReadPendingMessagesAmountQ()->getSingleScalarResult();
    }
}
