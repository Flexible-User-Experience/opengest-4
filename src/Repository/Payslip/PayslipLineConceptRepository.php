<?php

namespace App\Repository\Payslip;

use App\Entity\Payslip\PayslipLineConcept;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class PayslipLineConceptRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PayslipLineConcept::class);
    }

    public function getPayslipLineConceptsSortedByNameQB(): QueryBuilder
    {
        return $this->createQueryBuilder('c')->orderBy('c.name');
    }

    public function getPayslipLineConceptsSortedByNameQ(): Query
    {
        return $this->getPayslipLineConceptsSortedByNameQB()->getQuery();
    }

    public function getPayslipLineConceptsSortedByName(): array
    {
        return $this->getPayslipLineConceptsSortedByNameQ()->getResult();
    }
}
