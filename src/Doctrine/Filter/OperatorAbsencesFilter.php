<?php

namespace App\Doctrine\Filter;

use DateTime;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class OperatorAbsencesFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string
    {
        if ('App\Entity\Operator\OperatorAbsence' != $targetEntity->getReflectionClass()->name) {
            return '';
        }
        $date = new DateTime();
        $year = $date->format('Y') * 1 - 1;

        return sprintf('YEAR(%s.%s) >= %s', $targetTableAlias, 'begin', $year);
    }
}
