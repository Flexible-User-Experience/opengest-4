<?php

namespace App\Security;

use App\Entity\Enterprise\Enterprise;
use App\Entity\Setting\User;
use App\Security\Traits\VoteOnAttributeTrait;

/**
 * Class EnterpriseVoter.
 */
class EnterpriseVoter extends AbstractVoter
{
    use VoteOnAttributeTrait;

    /**
     * @param string     $attribute
     * @param Enterprise $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof Enterprise && in_array($attribute, self::ATTRIBUTES);
    }

    /**
     * @param User|null|object $user
     * @param Enterprise       $enterprise
     *
     * @return bool
     */
    private function isOwner(?User $user, Enterprise $enterprise)
    {
        if (!$user) {
            return false;
        }

        $result = false;
        $users = $enterprise->getUsers();
        /* @var User $enterpriseUser */
        foreach ($users as $enterpriseUser) {
            if ($user->getId() == $enterpriseUser->getId()) {
                $result = true;
                break;
            }
        }

        return $result;
    }
}
