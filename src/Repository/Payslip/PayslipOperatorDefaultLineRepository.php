<?php

namespace App\Repository\Payslip;

use App\Entity\Payslip\PayslipOperatorDefaultLine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class PayslipOperatorDefaultLineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PayslipOperatorDefaultLine::class);
    }

    public function getPayslipOperatorDefaultLinesSortedByNameQB(): QueryBuilder
    {
        return $this->createQueryBuilder('c')->orderBy('c.name');
    }

    public function getPayslipOperatorDefaultLinesSortedByNameQ(): Query
    {
        return $this->getPayslipOperatorDefaultLinesSortedByNameQB()->getQuery();
    }

    public function getPayslipOperatorDefaultLinesSortedByName(): array
    {
        return $this->getPayslipOperatorDefaultLinesSortedByNameQ()->getResult();
    }
}
