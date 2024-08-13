<?php

namespace App\Security;

use App\Entity\Operator\Operator;
use App\Entity\Setting\User;
use App\Security\Traits\VoteOnAttributeTrait;

/**
 * Class OperatorVoter.
 */
class OperatorVoter extends AbstractVoter
{
    use VoteOnAttributeTrait;

    /**
     * @param string   $attribute
     * @param Operator $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof Operator && in_array($attribute, self::ATTRIBUTES);
    }

    /**
     * @param User|null|object $user
     * @param Operator         $operator
     *
     * @return bool
     */
    private function isOwner(?User $user, Operator $operator): bool
    {
        if (!$user) {
            return false;
        }

        return $operator->getEnterprise()->getId() == $user->getLoggedEnterprise()->getId();
    }
}
