<?php

namespace App\APIModel\Auth;

use SSH\MyJwtBundle\Request\CommonParameterBag;
use Symfony\Component\Validator\Constraints as Assert;

class Authentication extends CommonParameterBag
{
    #[Assert\NotBlank]
    public string $username;

    #[Assert\NotBlank]
    public string $intention;

    #[Assert\NotBlank]
    public string $password;
}