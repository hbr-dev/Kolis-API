<?php

namespace App\Manager;

use App\Entity\Client;
use App\Manager\AbstractManager;
use Doctrine\ORM\EntityManagerInterface;
use SSH\MyJwtBundle\Entity\ApiUser;
use SSH\MyJwtBundle\Manager\ExceptionManager;
use Symfony\Component\HttpFoundation\RequestStack;


class ClientManager extends AbstractManager
{
    /**
     * @var Client
     */
    private $client;

    /**
     * Undocumented variable
     *
     * @var ExceptionManager
     */
    private $exceptionManager;

    private $code;
    private $em;





    public function __construct(RequestStack $requestStack, ExceptionManager $exceptionManager, EntityManagerInterface $em) 
    {
        $this->exceptionManager = $exceptionManager;
        $this->em = $em;
        parent::__construct($requestStack, $em);
    }



    public function init($settings = [])
    {
        parent::setSettings($settings);

        if ($this->getCode()) {
            $filters = ['code' => $this->getCode()];
           
            $this->client = $this->em
                                        ->getRepository(Client::class)
                                        ->findOneBy($filters);

            if (empty($this->client)) {
                $this->exceptionManager->throwNotFoundException('UNKNOWN_CLIENT');
            }
        }

        return $this;
    }



    public function getClient($array = false) 
    {
        if ($array) {
            $client = $this->client->toArray();
            unset($client['password']);
            return ['data' => $client];
        }

        return $this->client;
    }



    public function getCode() {
        return $this->code;
    }



    public function setCode($code) {
        $this->code = $code;
        return $this;

    }



    public function getClients($defaultPage = null, $size = null) {
        return $this->getObjectsWithPagination('Client', page:$defaultPage, itemsPerPage: $size);
    }



    public function createClient() 
    {
        // the password hashed from the frontend
        $data = (array) $this->request->get('client');
        $client = $this->insertObject($data, Client::class, 'phoneNumber', ['phoneNumber' => $data['phoneNumber']]);

        $data = [
            "code" => $client->getCode(),
            "role" => "MOBILE,CLIENT",
            "mail" => ($client->getEmail() ? $client->getEmail() : "__@__.com"),
            "username" => $client->getPhoneNumber(),
            "password" => $client->getPassword(),
            "roles" => "[]"
        ];
        $user = $this->insertObject($data, ApiUser::class, 'username', ['username' => $data['username']]);

        return ['data' => [
                'messages' => 'create_success',
                'object' => $client->getCode(),
                'user' => $user->getId()
        ]];
    } 



    public function editClient()
    {
        // the password hashed from the frontend
        $data = (array) $this->request->get('_client');
        if($data['password'] != $this->client->getPassword()) {
            return ['data' => [
                'messages' => 'unauthorized_action',
            ]];
        } else {
            if ($data['new_password']) {
                $data['password'] = $data['new_password'];
            }
            return $this->updateObject(Client::class, $this->client, $data, 'phoneNumber', ['phoneNumber' => $data['phoneNumber']]);
        }
    }   
    
    
    
    public function bulkUpdateStatuses($statusField, $value)
    {
        $setterMethod = 'set' . ucfirst($statusField);
        $this->client->$setterMethod($value);

        return $this->patchObject(Client::class, $this->client);
    }



    public function deleteClient() 
    {
        return $this->deleteObject($this->client);
    }
}
