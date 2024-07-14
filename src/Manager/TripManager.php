<?php

namespace App\Manager;

use App\Entity\Transporter;
use App\Entity\Trip;
use App\Manager\AbstractManager;
use DateTime;
use DateTimeZone;
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



    public function getTrips($defaultPage = null, $size = null)
    {
        return $this->getObjectsWithPagination('Trip', page: $defaultPage, itemsPerPage: $size);
    }



    private function getRelatedPackages()
    {
        $packages = [];
        $criteria = ['trip' => $this->trip];

        $orderBy = ['createdAt' => 'DESC'];

        $packagesAsObjects = $this->getObjectsByCriteria("Package", $criteria, orderBy: $orderBy);

        foreach ($packagesAsObjects as $object) {
            $package = $object->toArray();
            $package["sender"] = [
                "firstName" => $object->getSender()->getFirstName(),
                "lastName" => $object->getSender()->getLastName(),
                "phoneNumber" => $object->getSender()->getPhoneNumber(),
                "profile_img" => $object->getSender()->getProfileImg()
            ];
            if ($object->getSender()->getEmail()) {
                $package["sender"]["email"] = $object->getSender()->getEmail();
            }
            $package["receiver"] = [
                "firstName" => $object->getReceiver()->getFirstName(),
                "lastName" => $object->getReceiver()->getLastName(),
                "phoneNumber" => $object->getReceiver()->getPhoneNumber(),
                "profile_img" => $object->getReceiver()->getProfileImg()
            ];
            if ($object->getReceiver()->getEmail()) {
                $package["receiver"]["email"] = $object->getReceiver()->getEmail();
            }
            $packages[$object->getId()] = $package;
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
    
        $criteria = [];
        $queryParams = $this->request->query->all();
    
        foreach ($queryParams as $key => $value) {
            $criteria[$key] = $value;
        }
    
        $today = new DateTime('now');
        $todayCET = $today->setTimezone(new DateTimeZone('CET'));  // Convert to UTC if needed
    
        $repository = $this->em->getRepository(Trip::class);
    
        $qb = $repository->createQueryBuilder('t');
    
        $qb->join('t.transporter', 'tr');
        
        $qb->where($qb->expr()->gt('t.date', ':todayCET'))
            ->setParameter('todayCET', $todayCET);
    
        // Apply additional criteria if provided
        if (!empty($criteria)) {
            foreach ($criteria as $field => $value) {
                if ($field === 'firstName' || $field === 'lastName' || $field === 'address' || $field === 'phoneNumber' || $field === 'email') {
                    $qb->andWhere($qb->expr()->like("tr.$field", ":$field"))
                        ->setParameter($field, '%'.$value.'%');
                } 
                else if ($field === 'date') {
                    $qb->andWhere($qb->expr()->eq("t.$field", ":$field")) 
                       ->setParameter($field, new \DateTime($value));
                }
                else {
                    $qb->andWhere($qb->expr()->like("t.$field", ":$field"))
                        ->setParameter($field, '%'.$value.'%');
                }
            }
        }
    
        $tripsAsObjects = $qb->getQuery()->getResult();
    
        foreach ($tripsAsObjects as $object) {
            $trips[$object->getId()] = $object->toArray();
            $transporterInfo = [
                "FirstName" => $object->getTransporter()->getFirstName(),
                "LastName" => $object->getTransporter()->getLastName(),
                "Address" => $object->getTransporter()->getAddress(),
                "PhoneNumber" => $object->getTransporter()->getPhoneNumber()
            ];
            if ($object->getTransporter()->getEmail()) {
                $transporterInfo["Email"] = $object->getTransporter()->getEmail();
            }
            $trips[$object->getId()]['transporter'] = $transporterInfo;
        }
    
        return ['data' => $trips];
    } 



    // public function getAvailableTrips()
    // {
    //     $trips = [];

    //     $criteria = [];
    //     $queryParams = $this->request->query->all();

    //     foreach ($queryParams as $key => $value) {
    //         $criteria[$key] = $value;
    //     }

    //     $today = new DateTime('now');
    //     $todayCET = $today->setTimezone(new DateTimeZone('CET'));  // Convert to UTC (adjust if needed)

    //     $repository = $this->em->getRepository(Trip::class);

    //     $qb = $repository->createQueryBuilder('t');

    //     $qb->where($qb->expr()->gt('t.date', ':todayCET'))
    //         ->setParameter('todayCET', $todayCET);

    //     // Apply additional criteria if provided
    //     if (!empty($criteria)) {
    //         foreach ($criteria as $field => $value) {
    //             $qb->andWhere($qb->expr()->like("t.$field", ":$field"))
    //                 ->setParameter($field, $value);
    //         }
    //     }

    //     $tripsAsObjects = $qb->getQuery()->getResult();

    //     foreach ($tripsAsObjects as $object) {
    //         $trips[$object->getId()] = $object->toArray();
    //         $transporterInfo = [
    //             "FirstName" => $object->getTransporter()->getFirstName(),
    //             "LastName" => $object->getTransporter()->getLastName(),
    //             "Address" => $object->getTransporter()->getAddress(),
    //             "PhoneNumber" => $object->getTransporter()->getPhoneNumber()
    //         ];
    //         if ($object->getTransporter()->getEmail()) {
    //             $transporterInfo["email"] = $object->getTransporter()->getEmail();
    //         }
    //         $trips[$object->getId()]['transporter'] = $transporterInfo;
    //     }

    //     return ['data' => $trips];
    // }




    // public function getAvailableTrips()
    // {
    //     $trips = [];

    //     $today = new DateTime('now');
    //     $todayCET = $today->setTimezone(new DateTimeZone('CET'));  // Convert to UTC (adjust if needed)

    //     $repository = $this->em->getRepository(Trip::class);

    //     $qb = $repository->createQueryBuilder('t');

    //     $qb->where($qb->expr()->gt('t.date', ':todayCET'))
    //        ->setParameter('todayCET', $todayCET);

    //     $tripsAsObjects = $qb->getQuery()->getResult();

    //     foreach ($tripsAsObjects as $object) {
    //         $trips[$object->getId()] = $object->toArray();
    //         $transporterInfo = [
    //             "FirstName" => $object->getTransporter()->getFirstName(),
    //             "LastName" => $object->getTransporter()->getLastName(),
    //             "Address" => $object->getTransporter()->getAddress(),
    //             "PhoneNumber" => $object->getTransporter()->getPhoneNumber()
    //         ];
    //         if ($object->getTransporter()->getEmail()) {
    //             $transporterInfo["email"] = $object->getTransporter()->getEmail();
    //         }
    //         $trips[$object->getId()]['transporter'] = $transporterInfo;
    //     }

    //     return ['data' => $trips];
    // }



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
