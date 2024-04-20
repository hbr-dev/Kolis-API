<?php

namespace App\Manager;

use App\Entity\Pack;
use App\Entity\Transporter;
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
     * @var Transporter
     */
    private $transporter;

    /**
     * @var ExceptionManager
     */
    private $exceptionManager;

    /**
     * @var TransporterManager
     */
    private $transporterManager;

    private $code;
    private $transporterCode;
    private $em;




    public function __construct(TransporterManager $transporterManager, RequestStack $requestStack, ExceptionManager $exceptionManager, EntityManagerInterface $em) 
    {
        $this->transporterManager = $transporterManager;
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

        if ($this->getTransporterCode()) {
            $this->transporter = $this->transporterManager
                                        ->init(['code' => $this->getTransporterCode()])
                                        ->getTransporter();
        }

        return $this;
    }



    public function getPack($array = false)
    {
        if ($array) {
            return ['data' => $this->pack->toArray()];
        }

        return $this->pack;
    }



    public function getTransporterPacks()
    {
        $packs = [];
        $packsAsObjects = $this->getObjectsByOwner("Pack", "transporter", $this->transporter);

        foreach ($packsAsObjects as $object) {
            $packs[$object->getId()] = $object->toArray();
        }

        return ['data' => $packs];
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



    public function getTransporterCode() 
    {
        return $this->transporterCode;
    }



    public function setTransporterCode($transporterCode) 
    {
        $this->transporterCode = $transporterCode;
        return $this;

    }



    public function createPack()
    {
        $data = (array) $this->request->get('pack');
        $data['expiration_date'] = new \DateTime($data['expiration_date']);

        $data['transporter'] = $this->transporter;

        $pack = $this->insertObject($data, Pack::class);

        return ['data' => [
                'messages' => 'create_success',
                'object' => $pack->getCode()
        ]];
    }
    

    
    public function updateSubscription()
    {
        $data = (array) $this->request->get('_pack');
        $data['expiration_date'] = new \DateTime($data['expiration_date']);

        return $this->updateObject(Pack::class, $this->pack, $data);
    }



    public function cancelSubscription() 
    {
        return $this->deleteObject($this->pack);
    }
}
