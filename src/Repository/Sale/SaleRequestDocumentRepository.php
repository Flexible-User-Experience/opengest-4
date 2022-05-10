<?php

namespace App\Repository\Setting;

use App\Entity\Sale\SaleRequestDocument;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class SaleRequestDocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SaleRequestDocument::class);
    }

    public function getSortedByNameQB(): QueryBuilder
    {
        return $this->createQueryBuilder('srd')->orderBy('srd.document');
    }

    public function getSortedByNameQ(): Query
    {
        return $this->getSortedByNameQB()->getQuery();
    }

    public function getSortedByName(): array
    {
        return $this->getSortedByNameQ()->getResult();
    }
}
