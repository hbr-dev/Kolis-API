<?php

namespace App\Manager;

use App\Entity\Pack;
use App\Manager\AbstractManager;
use Doctrine\ORM\EntityManagerInterface;
use SSH\MyJwtBundle\Manager\ExceptionManager;
use Symfony\Component\HttpFoundation\RequestStack;


class PackManager extends AbstractManager
{
    /**
     * @var Pack
     */
    private $pack;

    /**
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
    

    
    /**
     * Initializes the Pack
     * object, and its parent
     * object.
     *
     * @param array $settings
     */
    public function init($settings = [])
    {
        parent::setSettings($settings);

        if ($this->getCode()) {
            $filters = ['code' => $this->getCode()];
           
            $this->pack = $this->em
                                    ->getRepository(Pack::class)
                                    ->findOneBy($filters);

            if (empty($this->pack)) {
                $this->exceptionManager->throwNotFoundException('NO_SUBSCRIPTION_PACK_FOUND');
            }
        }

        return $this;
    }



    public function getCode() 
    {
        return $this->code;
    }



    public function setCode($code) 
    {
        $this->code = $code;
        return $this;

    }



    public function getPack($array = false)
    {
        if ($array) {
            return ['data' => $this->pack->toArray()];
        }

        return $this->pack;
    }



    public function getAllPacks()
    {
        $packs = [];
        $criteria = [];
        
        $packsAsObjects = $this->getObjectsByCriteria("Pack", $criteria);

        foreach ($packsAsObjects as $object) {
            $packs[$object->getId()] = $object->toArray();
        }

        return ['data' => $packs];
    }



    public function createPack()
    {
        $data = (array) $this->request->get('pack');

        $pack = $this->insertObject($data, Pack::class);

        return ['data' => [
                'messages' => 'create_success',
                'object' => $pack->getCode()
        ]];
    } 
}
