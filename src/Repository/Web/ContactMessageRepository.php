<?php

namespace App\Repository\Web;

use App\Entity\Web\ContactMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class ContactMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactMessage::class);
    }

    public function getPendingMessagesAmountQB(): QueryBuilder
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.answered = :answered')
            ->setParameter('answered', false)
        ;
    }

    public function getPendingMessagesAmountQ(): Query
    {
        return $this->getPendingMessagesAmountQB()->getQuery();
    }

    public function getPendingMessagesAmount(): int
    {
        try {
            $result = $this->getPendingMessagesAmountQ()->getSingleScalarResult();
        } catch (NoResultException $e) {
            $result = 0;
        } catch (NonUniqueResultException $e) {
            $result = 0;
        }

        return $result;
    }

    public function getReadPendingMessagesAmountQB(): QueryBuilder
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.checked = :checked')
            ->setParameter('checked', false)
        ;
    }

    public function getReadPendingMessagesAmountQ(): Query
    {
        return $this->getReadPendingMessagesAmountQB()->getQuery();
    }

    public function getReadPendingMessagesAmount(): int
    {
        try {
            $result = $this->getReadPendingMessagesAmountQ()->getSingleScalarResult();
        } catch (NoResultException $e) {
            $result = 0;
        } catch (NonUniqueResultException $e) {
            $result = 0;
        }

        return $result;
    }
}
