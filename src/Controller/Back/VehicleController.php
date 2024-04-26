<?php

namespace App\Controller\Back;

use App\Manager\VehicleManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/back")]
class VehicleController extends AbstractController
{

    private $manager;

    /**
     * Admin VehicleController constructor.
     */
    public function __construct(VehicleManager $manager) {
        $this->manager = $manager;
    }



    #[IsGranted("ROLE_ADMIN")]
    #[Route("/vehicles", name: "api_admin_get_vehicles", methods: ["GET"])]
    public function getVehicles()
    {
        return $this->manager
                        ->getVehicles();
    }
}
