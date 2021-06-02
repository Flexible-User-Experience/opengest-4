<?php

namespace App\Repository\Partner;

use App\Entity\Enterprise\Enterprise;
use App\Entity\Partner\Partner;
use App\Entity\Partner\PartnerType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class PartnerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Partner::class);
    }

    public function getEnabledSortedByNameQB(): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->where('p.enabled = :enabled')
            ->setParameter('enabled', true)
            ->orderBy('p.name', 'ASC')
        ;
    }

    public function getEnabledSortedByNameQ(): Query
    {
        return $this->getEnabledSortedByNameQB()->getQuery();
    }

    public function getEnabledSortedByName(): array
    {
        return $this->getEnabledSortedByNameQ()->getResult();
    }

    public function getFilteredByEnterpriseEnabledSortedByNameQB(Enterprise $enterprise): QueryBuilder
    {
        return $this->getEnabledSortedByNameQB()
            ->andWhere('p.enterprise = :enterprise')
            ->setParameter('enterprise', $enterprise)
        ;
    }

    public function getFilteredByEnterpriseEnabledSortedByNameQ(Enterprise $enterprise): Query
    {
        return $this->getFilteredByEnterpriseEnabledSortedByNameQB($enterprise)->getQuery();
    }

    public function getFilteredByEnterpriseEnabledSortedByName(Enterprise $enterprise): array
    {
        return $this->getFilteredByEnterpriseEnabledSortedByNameQ($enterprise)->getResult();
    }

    public function getFilteredByEnterprisePartnerTypeEnabledSortedByNameQB(Enterprise $enterprise, PartnerType $partnerType): QueryBuilder
    {
        return $this->getFilteredByEnterpriseEnabledSortedByNameQB($enterprise)
            ->andWhere('p.type = :partnerType')
            ->setParameter('partnerType', $partnerType)
        ;
    }

    public function getFilteredByEnterprisePartnerTypeEnabledSortedByNameQ(Enterprise $enterprise, PartnerType $partnerType): Query
    {
        return $this->getFilteredByEnterprisePartnerTypeEnabledSortedByNameQB($enterprise, $partnerType)->getQuery();
    }

    public function getFilteredByEnterprisePartnerTypeEnabledSortedByName(Enterprise $enterprise, PartnerType $partnerType): array
    {
        return $this->getFilteredByEnterprisePartnerTypeEnabledSortedByNameQ($enterprise, $partnerType)->getResult();
    }
}
