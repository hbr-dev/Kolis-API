<?php

namespace App\APIModel\Mobile;

use SSH\MyJwtBundle\Request\CommonParameterBag;
use Symfony\Component\Validator\Constraints as Assert;



class Client extends CommonParameterBag
{
    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z\s]{2,}$/',
        message: 'Invalid first name'
    )]
    public $firstName;

    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z\s]{2,}$/',
        message: 'Invalid last name'
    )]
    public $lastName;

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
    public $new_password;

    public $awaitingForDelivery = false;

    public $idVerified = false;

    public $active = true;

    public $profile_img = null;
}
