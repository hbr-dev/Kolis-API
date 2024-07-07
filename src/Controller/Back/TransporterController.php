<?php

namespace App\Controller\Back;

use App\Manager\TransporterManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


#[Route("/back")]
class TransporterController extends AbstractController
{

    private $manager;

    /**
     * Admin TransporterController constructor.
     */
    public function __construct(TransporterManager $manager)
    {
        $this->manager = $manager;
    }



    //#[IsGranted("ROLE_ADMIN")]
    #[Route("/transporter/{code}", name: "api_admin_get_transporter", methods: ["GET"])]
    public function getTransporter($code)
    {
        return $this->manager
                        ->init(["code" => $code])
                        ->getTransporter(true);
    }



    //#[IsGranted("ROLE_ADMIN")]
    #[Route("/transporters/{defaultPage}/{size}", name: "api_admin_get_transporters", methods: ["GET"])]
    public function getTransporters(int $defaultPage, int $size)
    {
        return $this->manager
                        ->getTransporters($defaultPage, $size);
    }



    //#[IsGranted("ROLE_ADMIN")]
    #[Route("/transorter/{code}/active/{status}", name: "api_admin_patch_active_status", methods: ["PATCH"])]
    public function updateActiveStatus($code, bool $status) {
        return $this->manager
                        ->init(["code" => $code])
                        ->bulkUpdateStatuses('active', $status);
    }
}
