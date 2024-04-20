<?php

namespace App\Manager;

use App\Manager\AbstractManager;
use Doctrine\ORM\EntityManagerInterface;
use SSH\MyJwtBundle\Entity\TokenApiUser;
use Symfony\Component\HttpFoundation\RequestStack;


class AuthManager extends AbstractManager
{
    // private $requestStack;
    // private $request;
    // private $login;




    // public function __construct(
    //     RequestStack $requestStack, 
    //     EntityManagerInterface $em,
    // ) {
    //     $this->requestStack = $requestStack;
    //     if ($requestStack instanceof RequestStack) {
    //         $this->request = $requestStack->getCurrentRequest();

    //         if (!$this->request) {
    //             throw new \Exception('No request found.');
    //         }
    //     }
    //     parent::__construct($em);
    // }



    // public function createLogin($jwtData) 
    // {
    //     // $data['jwt'] = hash(self::HASH_ALGO, $data['jwt']);

    //     if (!array_key_exists('jwt', $jwtData)) {
    //         return [
    //             "error" => "Unable to locate JWT"
    //         ];
    //     }

    //     $this->loggedInUser(new TokenApiUser(), $jwtData);

    //     return ['data' => [
    //             'messages' => 'loggedIn_success',
    //     ]];
    // } 



    // /**
    //  * Set function returns
    //  * the last valid login
    //  * for a user
    //  */
    // public function setLogin()
    // {
    //     // $user = $tokenManager->getUser($token, $userPorvider);
    //     $token = $this->request->headers->get('x-auth-token');

    //     $this->login = $this->getLoggedInObject(TokenApiUser::class, $token);

    //     return $this;
    // }  



    // public function logout() 
    // {
    //     return $this->deleteObject($this->login, prevSession: $this->login);
    // }
}
