<?php

namespace App\Manager;

use App\Entity\Package;
use App\Entity\Transporter;
use App\Entity\Trip;
use App\Manager\AbstractManager;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use SSH\MyJwtBundle\Manager\ExceptionManager;
use Symfony\Component\HttpFoundation\RequestStack;


class TripManager extends AbstractManager
{

    /**
     * @var Trip
     */
    private $trip;

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
     * Initializes the Package
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
           
            $this->trip = $this->em
                                    ->getRepository(Trip::class)
                                    ->findOneBy($filters);

            if (empty($this->trip)) {
                $this->exceptionManager->throwNotFoundException('UNKNOWN_TRIP');
            }
        }

        if ($this->getTransporterCode()) {
            $this->transporter = $this->transporterManager
                                        ->init(['code' => $this->getTransporterCode()])
                                        ->getTransporter();
        }

        return $this;
    }



    public function getTrip($array = false)
    {
        if ($array) {
            return [
                'data' => $this->trip->toArray(),
                'packages' => $this->getRelatedPackages()
            ];
        }

        return $this->trip;
    }



    private function getRelatedPackages()
    {
        $packages = [];
        $criteria = ['trip' => $this->trip];
          
        $orderBy = ['createdAt' => 'DESC'];
        
        $packagesAsObjects = $this->getObjectsByCriteria("Package", $criteria, orderBy:$orderBy);

        foreach ($packagesAsObjects as $object) {
            $packages[$object->getId()] = $object->toArray();
        }

        return $packages;
    }



    public function getTransporterTrips()
    {
        $trips = [];
        $tripsAsObjects = $this->getObjectsByOwner("Trip", "transporter", $this->transporter);

        foreach ($tripsAsObjects as $object) {
            $trips[$object->getId()] = $object->toArray();
        }

        return ['data' => $trips];
    }



    public function getAvailableTrips()
    {
        $trips = [];

        $today = new DateTime();

        $criteria = [
            "date" => [
                '<',
                $today->format('Y-m-d')
            ]
        ];

        $tripsAsObjects = $this->getObjectsByCriteria("Trip", $criteria);
        
        foreach ($tripsAsObjects as $object) {
            $trips[$object->getId()] = $object->toArray();
        }

        return ['data' => $trips];
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



    public function createTrip()
    {
        $data = (array) $this->request->get('trip');
        $data['status'] = self::TRIP_STATUS_CREATED;
        $data['date'] = new \DateTime($data['date']);
        $data['transporter'] = $this->transporter;

        $trip = $this->insertObject($data, Trip::class);

        return ['data' => [
            'messages' => 'create_success',
            'object' => $trip->getCode()
        ]];
    }



    public function editTrip()
    {
        $data = (array) $this->request->get('_trip');
        $data['date'] = new \DateTime($data['date']);
        if ($this->trip->getStatus() == self::TRIP_STATUS_CREATED) {
            return $this->updateObject(Trip::class, $this->trip, $data);
        } else {
            return [
                "data" => "Unable to update confirmed trip."
            ];
        }
    }



    public function updateTripStatus($status)
    {
        $validStatuses = [
            'created' => self::TRIP_STATUS_CREATED,
            'confirmed' => self::TRIP_STATUS_CONFIRMED,
            'in_progress' => self::TRIP_STATUS_IN_PROGRESS,
            'accomplished' => self::TRIP_STATUS_ACCOMPLISHED,
            'cancelled' => self::TRIP_STATUS_CANCELED
        ];
        if (array_key_exists($status, $validStatuses)) {
            $this->trip->setStatus($validStatuses[$status]);
            return $this->patchObject(Trip::class, $this->trip);
        } else {
            return [
                "data" => "Invalid status."
            ];
        }
    }



    public function deleteTrip()
    {
        return $this->deleteObject($this->trip);
    }
}
