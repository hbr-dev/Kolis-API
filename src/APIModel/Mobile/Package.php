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
    public $description;
    
    #[Assert\NotBlank]
    public $weight;
    
    #[Assert\NotBlank]
    #[Assert\Count(
        min: 3, 
        max: 3,
        minMessage: "Dimensions array must have exactly 3 elements",
        maxMessage: "Dimensions array must have exactly 3 elements"
     )]
    public $dimensions;


    
    #[Assert\Regex(
        pattern: "/^\d{1,3}(\.\d{1,3})?$/",
        message: 'Transportation charges not valid. eg. 105.650'
    )]
    public $transportationCharge;

    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z\s]{2,}$/',
        message: 'Invalid receiver first name'
    )]
    public $receiverFirstName;


    #[Assert\Regex(
        pattern: '/^[a-zA-Z\s]{2,}$/',
        message: 'Invalid receiver last name'
    )]
    #[Assert\NotBlank]
    public $receiverLastName;

    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/^([2459]\d{7}|[67]\d{8})$/',
        message: 'Invalid receiver phone number. Please enter 
                  a valid Tunisia or France mobile number.'
    )]
    public $receiverPhoneNumber;

    #[Assert\Regex(
        pattern: "/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/",
        message: "Invalid receiver email address"
    )]
    public $receiverEmail;

}
