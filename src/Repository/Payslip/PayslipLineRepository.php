<?php

namespace App\Repository\Payslip;

use App\Entity\Payslip\PayslipLine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class PayslipLineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PayslipLine::class);
    }

    public function getPayslipLinesSortedByNameQB(): QueryBuilder
    {
        return $this->createQueryBuilder('c')->orderBy('c.name');
    }

    public function getPayslipLinesSortedByNameQ(): Query
    {
        return $this->getPayslipLinesSortedByNameQB()->getQuery();
    }

    public function getPayslipLinesSortedByName(): array
    {
        return $this->getPayslipLinesSortedByNameQ()->getResult();
    }
}
