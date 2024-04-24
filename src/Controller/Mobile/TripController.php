<?php

namespace App\Controller\Mobile;

use App\Entity\Trip;
use App\Manager\TripManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use SSH\MyJwtBundle\Annotations\Mapping;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

#[Route("/mobile")]
class TripController extends AbstractController
{

    private $manager;

    /**
     * TripController constructor.
     */
    public function __construct(TripManager $manager) {
        $this->manager = $manager;
    }



    #[IsGranted('ROLE_TRANSPORTER')]
    /**
     * @Route("/trip", name="api_new_trip", methods={"POST"})
     * @Mapping(object="App\APIModel\Mobile\Trip", as="trip")
     */
    public function createTrip()
    {
        return $this->manager
                        ->init(['transporterCode'=>$this->getUser()->getCode()])
                        ->createTrip();
    }



    #[IsGranted('ROLE_MOBILE')]
    #[Route("/trip/{code}", name: "api_get_trip", methods: ["GET"])]
    public function getTrip($code) 
    {
        return $this->manager
                        ->init(['code' => $code])
                        ->getTrip(true);
    }



    #[IsGranted('ROLE_TRANSPORTER')]
    #[Route("/trips", name: "api_get_my_trips", methods: ["GET"])]
    public function getTransporterTrips() 
    {
        return $this->manager
                        ->init(['transporterCode'=>$this->getUser()->getCode()])
                        ->getTransporterTrips();
    }



    #[IsGranted("ROLE_TRANSPORTER")]
    #[Route("/trip/{code}/packages", name: "api_get_related_packages", methods: ["GET"])]
    public function getRelatedPackages($code)
    {
        return $this->manager
                        ->init(['code' => $code])
                        ->getRelatedPackages();
    }



    #[IsGranted('ROLE_TRANSPORTER')]
    #[Route("/trip/{code}", name: "api_delete_trip", methods: ["DELETE"])]
    public function deleteTrip($code, Trip $trip, AuthorizationCheckerInterface $authorizationChecker) 
    {
        
        if (!$authorizationChecker->isGranted('VIEW', $trip)) {
            throw new AccessDeniedHttpException('Access denied.');
        }

        return $this->manager
                        ->init(['code' => $code])
                        ->deleteTrip();
    }



    #[IsGranted('ROLE_TRANSPORTER')]
     /**
     * @Route("/trip/{code}", name="api_update_trip", methods={"POST"})
     * @Mapping(object="App\APIModel\Mobile\Trip", as="_trip")
     */
    public function editTrip($code, Trip $trip, AuthorizationCheckerInterface $authorizationChecker)
    {
        
        if (!$authorizationChecker->isGranted('VIEW', $trip)) {
            throw new AccessDeniedHttpException('Access denied.');
        }

        return $this->manager
                        ->init(['code' => $code])
                        ->editTrip();
    }



    #[IsGranted('ROLE_TRANSPORTER')]
    #[Route("/trip/{code}/{status}", name: "api_patch_trip_status", methods: ["PATCH"])]
    public function updateTripStatus($code, string $status, Trip $trip, AuthorizationCheckerInterface $authorizationChecker) 
    {
        
        if (!$authorizationChecker->isGranted('VIEW', $trip)) {
            throw new AccessDeniedHttpException('Access denied.');
        }

        return $this->manager
                        ->init(['code' => $code])
                        ->updateTripStatus($status);
    }
}
