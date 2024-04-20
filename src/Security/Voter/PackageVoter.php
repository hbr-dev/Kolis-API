<?php

namespace App\Security\Voter;

use App\Entity\Package;
use SSH\MyJwtBundle\Entity\ApiUser;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;




class PackageVoter extends Voter
{
    protected function supports(string $attribute, $object): bool
    {
        return $object instanceof Package && in_array($attribute, ['VIEW']);
    }


    
    protected function voteOnAttribute(string $attribute, $package, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof ApiUser) {
            return false;
        }

        return $package->getSender()->getCode() == $user->getCode();
    }
}
