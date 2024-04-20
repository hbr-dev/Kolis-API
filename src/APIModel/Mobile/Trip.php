<?php

namespace App\APIModel\Mobile;


use SSH\MyJwtBundle\Request\CommonParameterBag;
use Symfony\Component\Validator\Constraints as Assert;


class Trip extends CommonParameterBag
{
    #[Assert\NotBlank]
    #[Assert\Length(
        max: 300,
        maxMessage: 'Pick-up location so long(max 300 characters)'
    )]
    public string $pickUPLocation;

    #[Assert\NotBlank]
    #[Assert\Length(
        max: 300,
        maxMessage: 'Delivery location so long(max 300 characters)'
    )]
    public string $deliveryLocation;

    #[Assert\NotBlank]
    public $date;

}
