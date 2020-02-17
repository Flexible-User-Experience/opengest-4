<?php

namespace App\Security\Traits;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Trait VoteOnAttributeTrait.
 */
trait VoteOnAttributeTrait
{
    /**
     * @param string         $attribute
     * @param object         $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        switch ($attribute) {
            case self::EDIT:
                return $this->isOwner($token->getUser(), $subject);
        }

        throw new \LogicException('Invalid attribute: '.$attribute);
    }
}
