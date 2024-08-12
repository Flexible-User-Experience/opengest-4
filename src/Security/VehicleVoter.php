<?php

namespace App\Security;

use App\Entity\Setting\User;
use App\Entity\Vehicle\Vehicle;
use App\Security\Traits\VoteOnAttributeTrait;

/**
 * Class VehicleVoter.
 */
class VehicleVoter extends AbstractVoter
{
    use VoteOnAttributeTrait;

    /**
     * @param string  $attribute
     * @param Vehicle $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof Vehicle && in_array($attribute, self::ATTRIBUTES);
    }

    /**
     * @param User|null|object $user
     * @param Vehicle          $vehicle
     *
     * @return bool
     */
    private function isOwner(?User $user, Vehicle $vehicle): bool
    {
        if (!$user) {
            return false;
        }

        return $vehicle->getEnterprise()->getId() == $user->getLoggedEnterprise()->getId();
    }
}
