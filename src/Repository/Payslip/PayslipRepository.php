<?php

namespace App\Repository\Payslip;

use App\Entity\Payslip\Payslip;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class PayslipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payslip::class);
    }

    public function getPayslipsSortedByNameQB(): QueryBuilder
    {
        return $this->createQueryBuilder('c')->orderBy('c.name');
    }

    public function getPayslipsSortedByNameQ(): Query
    {
        return $this->getPayslipsSortedByNameQB()->getQuery();
    }

    public function getPayslipsSortedByName(): array
    {
        return $this->getPayslipsSortedByNameQ()->getResult();
    }
}
