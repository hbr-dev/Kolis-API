<?php

namespace App\APIModel\Mobile;


use SSH\MyJwtBundle\Request\CommonParameterBag;
use Symfony\Component\Validator\Constraints as Assert;


class Trip extends CommonParameterBag
{
    #[Assert\NotBlank]
    #[Assert\Length(
        max: 300,
        maxMessage: 'Trip route so long(max 300 characters)'
    )]
    public $route;

    public $pickUPLat = null;

    public $pickUPLong = null;
    
    public $deliveryLat = null;
    
    public $deliveryLong = null;

    #[Assert\NotBlank]
    public $date;

}
