<?php

namespace App\APIModel\Auth;

use SSH\MyJwtBundle\Request\CommonParameterBag;
use Symfony\Component\Validator\Constraints as Assert;

class Authentication extends CommonParameterBag
{
    #[Assert\NotBlank]
    public $username;

    #[Assert\NotBlank]
    public $intention;

    #[Assert\NotBlank]
    public $password;
}
