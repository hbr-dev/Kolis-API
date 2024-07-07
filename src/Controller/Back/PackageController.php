<?php

namespace App\Controller\Back;

use App\Manager\PackageManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/back")]
class PackageController extends AbstractController
{

    private $manager;

    /**
     * Admin VehicleController constructor.
     */
    public function __construct(PackageManager $manager) {
        $this->manager = $manager;
    }



    //#[IsGranted("ROLE_ADMIN")]
    #[Route("/packages", name: "api_admin_get_trips", methods: ["GET"])]
    public function getPackages()
    {
        return $this->manager
                        ->getPackages();
    }
}
