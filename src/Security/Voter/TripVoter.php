<?php

namespace App\Security\Voter;

use App\Entity\Trip;
use SSH\MyJwtBundle\Entity\ApiUser;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;




class TripVoter extends Voter
{
    protected function supports(string $attribute, $object): bool
    {
        return $object instanceof Trip && in_array($attribute, ['VIEW']);
    }


    
    protected function voteOnAttribute(string $attribute, $trip, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof ApiUser) {
            return false;
        }

        return $trip->getTransporter()->getCode() == $user->getCode();
    }
}
