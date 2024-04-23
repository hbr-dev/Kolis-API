<?php

namespace App\APIModel\Mobile;

use SSH\MyJwtBundle\Request\CommonParameterBag;
use Symfony\Component\Validator\Constraints as Assert;





class Transporter extends CommonParameterBag
{

    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z]{2,}$/',
        message: 'Invalid first name'
    )]
    public $firstName;

    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z]{2,}$/',
        message: 'Invalid last name'
    )]
    public $lastName;
    
    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9\s.,#:-]{10,255}$/',
        message: 'Please enter a valid address'
    )]
    public $address;
    
    
    #[Assert\Regex(
        pattern: "/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/",
        message: "Invalid email address"
    )]
    public $email;

    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/^(216|33)$/',
        message: 'Invalid mobile country code. Please enter either 216 for Tunisia or 33 for France.'
    )]
    public $countryCode;

    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/^([2459]\d{7}|[67]\d{8})$/',
        message: 'Invalid phone number. Please enter 
                  a valid Tunisia or France mobile number.'
    )]
    public $phoneNumber;

    #[Assert\NotBlank]
    #[Assert\Length(
        min: 6,
        minMessage: 'Password must be at least 6 characters long.'
    )]
    #[Assert\Regex(
        pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[a-zA-Z\d!@#$%^&*]+$/',
        message: 'Password must contain at least one lowercase letter, 
                  one uppercase letter, one digit, and one special 
                  character !@#$%^&*'
    )]
    public $password;

    public $idVerified = false;

    public $active = false;

    public $profile_img = null;

}
