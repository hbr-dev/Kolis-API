<?php

namespace App\Controller\Mobile;

use App\Entity\Package;
use App\Manager\PackageManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use SSH\MyJwtBundle\Annotations\Mapping;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

#[Route("/mobile")]
class PackageController extends AbstractController
{

    private $manager;

    /**
     * PackageController constructor.
     */
    public function __construct(PackageManager $manager)
    {
        $this->manager = $manager;
    }



    #[IsGranted("ROLE_CLIENT")]
    /**
     * @Route("/package/{receiverCode}", name="api_new_package", methods={"POST"})
     * @Mapping(object="App\APIModel\Mobile\Package", as="package")
     */
    public function createPackage($receiverCode)
    {
        return $this->manager
                        ->init([
                            'senderCode' => $this->getUser()->getCode(),
                            'receiverCode' => $receiverCode
                        ])
                        ->createPackage();
    }



    #[IsGranted("ROLE_MOBILE")]
    #[Route("/package/{code}", name: "api_get_package", methods: ["GET"])]
    public function getPackage($code)
    {
        return $this->manager
                        ->init(['code' => $code])
                        ->getPackage(true);
    }



    #[IsGranted("ROLE_CLIENT")]
    #[Route("/package/{code}", name: "api_delete_package", methods: ["DELETE"])]
    public function deletePackage($code, Package $package, AuthorizationCheckerInterface $authorizationChecker)
    {
        
        if (!$authorizationChecker->isGranted('VIEW', $package)) {
            throw new AccessDeniedHttpException('Access denied.');
        }
        
        return $this->manager
                        ->init(['code' => $code])
                        ->deletePackage();
    }



    #[IsGranted("ROLE_CLIENT")]
    /**
     * @Route("/package/{code}/update", name="api_update_package", methods={"POST"})
     * @Mapping(object="App\APIModel\Mobile\Package", as="_package")
     */
    public function editPackage($code, Package $package, AuthorizationCheckerInterface $authorizationChecker)
    {
        
        if (!$authorizationChecker->isGranted('VIEW', $package)) {
            throw new AccessDeniedHttpException('Access denied.');
        }

        return $this->manager
                        ->init(['code' => $code])
                        ->editPackage();
    }



    #[IsGranted("ROLE_TRANSPORTER")]
    #[Route("/package/{code}/charges/{transportationCharges}", name: "api_patch_package_charges", methods: ["PATCH"])]
    public function updateTransportationCharges($code, $transportationCharges)
    {
        return $this->manager
                        ->init(['code' => $code])
                        ->updateTransportationCharge($transportationCharges);
    }



    #[IsGranted("ROLE_CLIENT")]
    #[Route("/package/{code}/shipment_demand/{tripCode}", name: "api_demand_a_shipment", methods: ["PATCH"])]
    public function shipmentDemand(Package $package, AuthorizationCheckerInterface $authorizationChecker, $code, $tripCode)
    {
        
        if (!$authorizationChecker->isGranted('VIEW', $package)) {
            throw new AccessDeniedHttpException('Access denied.');
        }

        return $this->manager
                        ->init(['code' => $code, 'tripCode' => $tripCode])
                        ->shipmentDemand();
    }



    #[IsGranted("ROLE_CLIENT")]
    #[Route("/package/{code}/cancel/shipment_demand", name: "api_cancel_a_demand_of_shipment", methods: ["PATCH"])]
    public function cancelDemand($code, Package $package, AuthorizationCheckerInterface $authorizationChecker,)
    {
        
        if (!$authorizationChecker->isGranted('VIEW', $package)) {
            throw new AccessDeniedHttpException('Access denied.');
        }

        return $this->manager
                        ->init(['code' => $code])
                        ->shipmentDemand(cancelled: true);
    }



    #[IsGranted("ROLE_TRANSPORTER")]
    #[Route("/package/{code}/approve/shipment_demand", name: "api_approve_a_demand_of_shipment", methods: ["PATCH"])]
    public function approveDemand($code)
    {
        return $this->manager
                        ->init(['code' => $code])
                        ->shipmentDemand(approved: true);
    }



    #[IsGranted("ROLE_TRANSPORTER")]
    #[Route("/package/{code}/decline/shipment_demand", name: "api_decline_a_demand_of_shipment", methods: ["PATCH"])]
    public function declineDemand($code)
    {
        return $this->manager
                        ->init(['code' => $code])
                        ->shipmentDemand(declined: true);
    }



    #[IsGranted("ROLE_TRANSPORTER")]
    #[Route("/package/{code}/report-lost", name: "api_report_package_lost", methods: ["PATCH"])]
    public function packageLost($code)
    {
        return $this->manager
                        ->init(['code' => $code])
                        ->updatePackageStatus("lost");
    }



    #[IsGranted("ROLE_TRANSPORTER")]
    #[Route("/package/{code}/report-demage", name: "api_report_package_demage", methods: ["PATCH"])]
    public function packageDemaged($code)
    {
        return $this->manager
                        ->init(['code' => $code])
                        ->updatePackageStatus("damaged");
    }
}
