<?php

namespace App\APIModel\Mobile;

use SSH\MyJwtBundle\Request\CommonParameterBag;
use Symfony\Component\Validator\Constraints as Assert;





class Vehicle extends CommonParameterBag
{
    
    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z0-9\s'-]{2,50}$/",
        message: 'Vehicle model is not valid'
    )]
    public string $model;

    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/^(TN|FR)[0-9]{4,6}$/',
        message: 'Registration number not valid: eg. TN1234, FR987654'
    )]
    public string $registrationNBR;

}
