<?php

namespace App\Manager;

use App\Entity\Client;
use App\Entity\Package;
use App\Manager\AbstractManager;
use Doctrine\ORM\EntityManagerInterface;
use SSH\MyJwtBundle\Manager\ExceptionManager;
use Symfony\Component\HttpFoundation\RequestStack;


class PackageManager extends AbstractManager
{
    /**
     * @var Package
     */
    private $package;

    /**
     * @var Client
     */
    private $sender;

    /**
     * @var Trip
     */
    private $trip;

    /**
     * @var Client
     */
    private $receiver;

    /**
     * @var ExceptionManager
     */
    private $exceptionManager;

    /**
     * @var ClientManager
     */
    private $clientManager;

    /**
     * @var TripManager
     */
    private $tripManager;

    private $receiverCode;
    private $senderCode;
    private $tripCode;
    private $code;
    private $em;




    public function __construct(TripManager $tripManager, ClientManager $clientManager, RequestStack $requestStack, ExceptionManager $exceptionManager, EntityManagerInterface $em)
    {
        $this->clientManager = $clientManager;
        $this->tripManager = $tripManager;
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

            $this->package = $this->em
                ->getRepository(Package::class)
                ->findOneBy($filters);

            if (empty($this->package)) {
                $this->exceptionManager->throwNotFoundException('UNKNOWN_PACKAGE');
            }
        }

        if ($this->getSenderCode()) {
            $this->sender = $this->clientManager
                ->init(['code' => $this->getSenderCode()])
                ->getClient();
        }

        if ($this->getReceiverCode()) {
            $this->receiver = $this->clientManager
                ->init(['code' => $this->getReceiverCode()])
                ->getClient();
        }

        if ($this->getTripCode()) {
            $this->trip = $this->tripManager
                ->init(['code' => $this->getTripCode()])
                ->getTrip();
        }

        return $this;
    }



    public function getPackage($array = false)
    {
        if ($array) {
            return ['data' => $this->package->toArray()];
        }

        return $this->package;
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



    public function getSenderCode()
    {
        return $this->senderCode;
    }



    public function setSenderCode($senderCode)
    {
        $this->senderCode = $senderCode;
        return $this;
    }



    public function getReceiverCode()
    {
        return $this->receiverCode;
    }



    public function setReceiverCode($receiverCode)
    {
        $this->receiverCode = $receiverCode;
        return $this;
    }



    public function getTripCode()
    {
        return $this->tripCode;
    }



    public function setTripCode($tripCode)
    {
        $this->tripCode = $tripCode;
        return $this;
    }



    public function createPackage()
    {
        $data = (array) $this->request->get('package');
        $data['status'] = self::PACKAGE_STATUS_CREATED;
        $data['sender'] = $this->sender;
        $data['receiver'] = $this->receiver;

        $package = $this->insertObject($data, Package::class);

        return ['data' => [
            'messages' => 'create_success',
            'object' => $package->getCode()
        ]];
    }



    public function editPackage()
    {
        $data = (array) $this->request->get('_package');

        if ($this->package->getStatus() == self::PACKAGE_STATUS_CREATED) {
            return $this->updateObject(Package::class, $this->package, $data);
        } else {
            return [
                "data" => "Accepting a package confirms its details and prevents further modifications."
            ];
        }
    }



    public function updatePackageStatus($status)
    {
        $validStatuses = [
            'created' => self::PACKAGE_STATUS_CREATED,
            'awaiting_for_trip' => self::PACKAGE_STATUS_AWAITING_TRIP,
            'approved' => self::PACKAGE_STATUS_APPROVED,
            'in_transit' => self::PACKAEG_STATUS_IN_TRANSIT,
            'delivered' => self::PACKAGE_STATUS_DELIVERED,
            'damaged' => self::PACKAGE_STATUS_DAMAGED,
            'lost' => self::PACKAGE_STATUS_LOST,
        ];
        if (array_key_exists($status, $validStatuses)) {
            $this->package->setStatus($validStatuses[$status]);
            return $this->patchObject(Package::class, $this->package);
        } else {
            return [
                "data" => "Invalid status."
            ];
        }
    }



    public function updateTransportationCharge($transportationCharge)
    {
        $this->package
                ->setTransportationCharge(floatval($transportationCharge));

        return $this->patchObject(Package::class, $this->package);
    }



    public function shipmentDemand($approved = false, $cancelled = false, $declined = false)
    {   
        $this->handleShipmentAction($approved, self::PACKAGE_STATUS_APPROVED);
        $this->handleShipmentAction($declined, self::PACKAGE_STATUS_DECLINED, true);
        $this->handleShipmentAction($cancelled, self::PACKAGE_STATUS_CREATED, true);

        if (!$approved && !$declined && !$cancelled) {
            $this->handleDefaultCase();
        }

        return $this->patchObject(Package::class, $this->package);
    }



    private function handleShipmentAction($flag, $packageStatus, $removeTrip = false)
    {
        if ($flag) {
            $this->package->setStatus($packageStatus);
            if ($removeTrip) {
                if ($this->package->getTrip()) {
                    $this->package->getTrip()->getPackages()->removeElement($this->package);
                    $this->package->setTrip(null);
                } else {
                    $this->exceptionManager->throwNotFoundException("No pending trip demand for this package");
                }
            }
        }
    }



    private function handleDefaultCase()
    {
        $this->package->setTrip($this->trip??null);
        $this->package->setStatus(self::PACKAGE_STATUS_AWAITING_TRIP);
    }



    public function deletePackage()
    {
        return $this->deleteObject($this->package);
    }
}
