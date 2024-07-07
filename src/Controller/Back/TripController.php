<?php

namespace App\Controller\Back;

use App\Manager\TripManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/back")]
class TripController extends AbstractController
{

    private $manager;

    /**
     * Admin VehicleController constructor.
     */
    public function __construct(TripManager $manager) {
        $this->manager = $manager;
    }



    #[IsGranted("ROLE_ADMIN")]
    #[Route("/trips/{defaultPage}/{size}", name: "api_admin_get_trips", methods: ["GET"])]
    public function getTrips(int $defaultPage, int $size)
    {
        return $this->manager
                        ->getTrips($defaultPage, $size);
    }
}
