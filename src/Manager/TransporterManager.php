<?php

namespace App\Manager;

use App\Entity\Transporter;
use App\Manager\AbstractManager;
use Doctrine\ORM\EntityManagerInterface;
use SSH\MyJwtBundle\Entity\ApiUser;
use SSH\MyJwtBundle\Manager\ExceptionManager;
use Symfony\Component\HttpFoundation\RequestStack;


class TransporterManager extends AbstractManager
{
    /**
     * @var Transporter
     */
    private $transporter;

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
           
            $this->transporter = $this->em
                                        ->getRepository(Transporter::class)
                                        ->findOneBy($filters);

            if (empty($this->transporter)) {
                $this->exceptionManager->throwNotFoundException('UNKNOWN_TRANSPORTER');
            }
        }

        return $this;
    }



    public function getTransporter($array = false)
    {
        if ($array) {
            return ['data' => $this->transporter->toArray()];
        }

        return $this->transporter;
    }



    public function getCode() {
        return $this->code;
    }



    public function setCode($code) {
        $this->code = $code;
        return $this;

    }



    public function createTransporter()
    {
        $data = (array) $this->request->get('transporter');
        $data['password'] = hash(self::HASH_ALGO, $data['password']);
        $transporter = $this->insertObject($data, Transporter::class, 'phoneNumber', ['phoneNumber' => $data['phoneNumber']]);
        
        $data = [
            "code" => $transporter->getCode(),
            "role" => "TRANSPORTER",
            "mail" => ($transporter->getEmail() ? $transporter->getEmail() : "__@__.com"),
            "username" => $transporter->getPhoneNumber(),
            "password" => $transporter->getPassword(),
            "roles" => "MOBILE,TRANSPORTER"
        ];
        $user = $this->insertObject($data, ApiUser::class, 'username', ['username' => $data['username']]);

        return ['data' => [
                'messages' => 'create_success',
                'object' => $transporter->getCode(),
                'user' => $user->getId()
        ]];
    }



    public function deleteTransporter() 
    {
        return $this->deleteObject($this->transporter);
    }



    public function editTransporter()
    {
        $data = (array) $this->request->get('_transporter');

        $data['password'] = hash(self::HASH_ALGO, $data['password']);
        if($data['password'] != $this->transporter->getPassword()) {
            return ['data' => [
                'messages' => 'unauthorized_action',
            ]];
        } else {
            return $this->updateObject(Transporter::class, $this->transporter, $data, 'phoneNumber', ['phoneNumber' => $data['phoneNumber']]);
        }
    }



    public function bulkUpdateStatuses($statusField, $value)
    {
        $setterMethod = 'set' . ucfirst($statusField);
        $this->transporter->$setterMethod($value);

        return $this->patchObject(Transporter::class, $this->transporter);
    }
}
