<?php

namespace App\Security\Voter;

use App\Entity\Vehicle;
use SSH\MyJwtBundle\Entity\ApiUser;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;




class VehicleVoter extends Voter
{
    protected function supports(string $attribute, $object): bool
    {
        return $object instanceof Vehicle && in_array($attribute, ['VIEW']);
    }


    
    protected function voteOnAttribute(string $attribute, $vehicle, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof ApiUser) {
            return false;
        }

        return $vehicle->getTransporter()->getCode() == $user->getCode();
    }
}
