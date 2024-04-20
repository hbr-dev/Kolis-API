<?php

namespace App\APIModel\Mobile;


use SSH\MyJwtBundle\Request\CommonParameterBag;
use Symfony\Component\Validator\Constraints as Assert;


class Package extends CommonParameterBag
{

    #[Assert\Length(
        max: 300,
        maxMessage: 'Paragraph length must be less than 300.'
    )]
    public string $description;
    
    #[Assert\NotBlank]
    public float $weight;
    
    #[Assert\NotBlank]
    #[Assert\Count(
        min: 3, 
        max: 3,
        minMessage: "Dimensions array must have exactly 3 elements",
        maxMessage: "Dimensions array must have exactly 3 elements"
     )]
    public $dimensions;
    
    /**
     *
     * @var \DecimalType
     */
    #[Assert\Regex(
        pattern: "/^\d{1,3}(\.\d{1,3})?$/",
        message: 'Transportation charges not valid. eg. 105.650'
    )]
    public $transportationCharge;

}
