<?php

namespace App\Security\Voter;

use App\Entity\Subscription;
use SSH\MyJwtBundle\Entity\ApiUser;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;




class SubscriptionVoter extends Voter
{
    protected function supports(string $attribute, $object): bool
    {
        return $object instanceof Subscription && in_array($attribute, ['VIEW']);
    }


    
    protected function voteOnAttribute(string $attribute, $subscription, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof ApiUser) {
            return false;
        }

        return $subscription->getTransporter()->getCode() == $user->getCode();
    }
}
