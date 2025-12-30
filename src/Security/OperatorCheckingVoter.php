<?php

namespace App\Security;

use App\Entity\Operator\OperatorChecking;
use App\Entity\Setting\User;
use App\Security\Traits\VoteOnAttributeTrait;

/**
 * Class OperatorCheckingVoter.
 */
class OperatorCheckingVoter extends AbstractVoter
{
    use VoteOnAttributeTrait;

    /**
     * @param string           $attribute
     * @param OperatorChecking $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof OperatorChecking && in_array($attribute, self::ATTRIBUTES);
    }

    /**
     * @param User|null|object $user
     * @param OperatorChecking $oc
     *
     * @return bool
     */
    private function isOwner(?User $user, OperatorChecking $oc): bool
    {
        if (!$user) {
            return false;
        }

        return $oc->getOperator()->getEnterprise()->getId() == $user->getLoggedEnterprise()->getId();
    }
}
