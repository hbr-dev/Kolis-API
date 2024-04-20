<?php

namespace App\Security\Voter;

use App\Entity\Pack;
use SSH\MyJwtBundle\Entity\ApiUser;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;




class PackVoter extends Voter
{
    protected function supports(string $attribute, $object): bool
    {
        return $object instanceof Pack && in_array($attribute, ['VIEW']);
    }


    
    protected function voteOnAttribute(string $attribute, $pack, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof ApiUser) {
            return false;
        }

        return $pack->getTransporter()->getCode() == $user->getCode();
    }
}
