<?php

namespace App\Doctrine\Filter;

use DateTime;
use Doctrine\DBAL\Platforms\MySQL80Platform;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class OperatorAbsencesFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string
    {
        if ('App\Entity\Operator\OperatorAbsence' != $targetEntity->getReflectionClass()->name) {
            return '';
        }
        // This check is required for the tests to work, as function YEAR does not work in SQLite
        if (!($this->getConnection()->getDatabasePlatform() instanceof MySQL80Platform)) {
            return '';
        }
        $date = new DateTime();
        $year = $date->format('Y') * 1 - 1;

        return sprintf('YEAR(%s.%s) >= %s', $targetTableAlias, 'begin', $year);
    }
}
