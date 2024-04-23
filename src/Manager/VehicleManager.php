<?php

namespace App\Manager;

use App\Entity\Transporter;
use App\Entity\Vehicle;
use App\Manager\AbstractManager;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use SSH\MyJwtBundle\Entity\TokenApiUser;
use SSH\MyJwtBundle\Manager\ExceptionManager;
use Symfony\Component\HttpFoundation\RequestStack;


class VehicleManager extends AbstractManager
{
    /**
     * @var Vehicle
     */
    private $vehicle;

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

    private $transporterCode;
    private $code;
    private $em;




    public function __construct(TransporterManager $transporterManager, RequestStack $requestStack, ExceptionManager $exceptionManager, EntityManagerInterface $em) 
    {
        $this->transporterManager = $transporterManager;
        $this->exceptionManager = $exceptionManager;
        $this->em = $em;

        parent::__construct($requestStack, $em);
    }    
    

    
    /**
     * Initializes the Vehicle
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
           
            $this->vehicle = $this->em
                                    ->getRepository(Vehicle::class)
                                    ->findOneBy($filters);

            if (empty($this->vehicle)) {
                $this->exceptionManager->throwNotFoundException('NO_VEHICLE_FOUND');
            }
        }

        if ($this->getTransporterCode()) {
            $this->transporter = $this->transporterManager
                                        ->init(['code' => $this->getTransporterCode()])
                                        ->getTransporter();
        }

        return $this;
    }    



    public function getVehicle($array = false)
    {
        if ($array) {
            return ['data' => $this->vehicle->toArray()];
        }

        return $this->vehicle;
    }



    public function getTransporterVehicles()
    {
        $vehicles = [];
        $vehiclesAsObjects = $this->getObjectsByOwner("Vehicle", "transporter", $this->transporter);

        foreach ($vehiclesAsObjects as $object) {
            $vehicles[$object->getId()] = $object->toArray();
        }

        return ['data' => $vehicles];
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



    public function createVehicle()
    {
        $data = (array) $this->request->get('vehicle');
        $data['transporter'] = $this->transporter;

        $vehicle = $this->insertObject($data, Vehicle::class);

        return ['data' => [
                'messages' => 'create_success',
                'object' => $vehicle->getCode()
        ]];
    }



    public function deleteVehicle() 
    {
        return $this->deleteObject($this->vehicle);
    }
}
