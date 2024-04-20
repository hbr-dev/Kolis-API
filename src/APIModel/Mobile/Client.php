<?php

namespace App\APIModel\Mobile;

use SSH\MyJwtBundle\Request\CommonParameterBag;
use Symfony\Component\Validator\Constraints as Assert;



class Client extends CommonParameterBag
{
    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z]{2,}$/',
        message: 'Invalid first name'
    )]
    public string $firstName;

    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z]{2,}$/',
        message: 'Invalid last name'
    )]
    public string $lastName;

    #[Assert\Regex(
        pattern: "/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/",
        message: "Invalid email address"
    )]
    public string $email;

    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/^(216|33)$/',
        message: 'Invalid mobile country code. Please enter either 216 for Tunisia or 33 for France.'
    )]
    public string $countryCode;

    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/^([2459]\d{7}|[67]\d{8})$/',
        message: 'Invalid phone number. Please enter 
                  a valid Tunisia or France mobile number.'
    )]
    public string $phoneNumber;

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
    public string $password;

    public bool $awaitingForDelivery = false;

    public bool $idVerified = false;

    public bool $active = false;

    public ?string $profile_img = null;
}
