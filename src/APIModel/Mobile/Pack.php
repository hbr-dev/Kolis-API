<?php

namespace App\APIModel\Mobile;

use SSH\MyJwtBundle\Request\CommonParameterBag;
use Symfony\Component\Validator\Constraints as Assert;





class Pack extends CommonParameterBag
{
    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: "/^\d{1,3}(\.\d{1,3})?$/",
        message: 'Price is not valid. eg. 105.650'
    )]
    public $price;
    

    
    #[Assert\NotBlank]
    public $expiration_date;
}
