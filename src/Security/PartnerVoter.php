<?php

namespace App\Security;

use App\Entity\Partner\Partner;
use App\Entity\Setting\User;
use App\Security\Traits\VoteOnAttributeTrait;

/**
 * Class PartnerVoter.
 */
class PartnerVoter extends AbstractVoter
{
    use VoteOnAttributeTrait;

    /**
     * @param string  $attribute
     * @param Partner $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof Partner && in_array($attribute, self::ATTRIBUTES);
    }

    /**
     * @param User|null|object $user
     * @param Partner          $partner
     *
     * @return bool
     */
    private function isOwner(?User $user, Partner $partner)
    {
        if (!$user) {
            return false;
        }

        return $partner->getEnterprise()->getId() == $user->getLoggedEnterprise()->getId();
    }
}
