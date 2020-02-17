<?php

namespace App\Security;

use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class AbstractVoter.
 */
abstract class AbstractVoter extends Voter
{
    /**
     * @var string
     */
    public const EDIT = 'edit';

    /**
     * @var array
     */
    public const ATTRIBUTES = [
        self::EDIT,
    ];
}
