<?php

namespace App\APIModel\Back;

use SSH\MyJwtBundle\Request\CommonParameterBag;
use Symfony\Component\Validator\Constraints as Assert;



class Pack extends CommonParameterBag
{
    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: "/^\d{1,3}(\.\d{1,3})?$/",
        message: 'Pack Price not valid. eg. 100.655'
    )]
    public $price;

    #[Assert\NotBlank]
    #[Assert\Length(
        max: 300,
        maxMessage: 'Pack Description length must be less than 300.'
    )]
    public $description;

    #[Assert\NotBlank]
    public $freeTrialPeriod;

    #[Assert\NotBlank]
    public $subscriptionPeriod;
}
