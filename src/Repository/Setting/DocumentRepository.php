<?php

namespace App\Repository\Setting;

use App\Entity\Setting\Document;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class DocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Document::class);
    }

    public function getSortedByDescriptionQB(): QueryBuilder
    {
        return $this->createQueryBuilder('d')->orderBy('d.description');
    }

    public function getSortedByDescriptionQ(): Query
    {
        return $this->getSortedByDescriptionQB()->getQuery();
    }

    public function getSortedByDescription(): array
    {
        return $this->getSortedByDescriptionQ()->getResult();
    }
}
