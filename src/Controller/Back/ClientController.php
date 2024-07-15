<?php

namespace App\Controller\Back;

use App\Manager\ClientManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


#[Route("/back")]
class ClientController extends AbstractController
{

    private $manager;

    /**
     * Admin ClientController constructor.
     */
    public function __construct(ClientManager $manager)
    {
        $this->manager = $manager;
    }



    //#[IsGranted("ROLE_ADMIN")]
    #[Route("/client/{code}", name: "api_admin_get_client", methods: ["GET"])]
    public function getClient($code)
    {
        return $this->manager
                        ->init(["code" => $code])
                        ->getClient(true);
    }



    //#[IsGranted("ROLE_ADMIN")]
    #[Route("/clients/{defaultPage}/{size}", name: "api_admin_get_clients", methods: ["GET"])]
    public function getClients(int $defaultPage, int $size)
    {
        return $this->manager
                        ->getClients($defaultPage, $size);
    }    
    

    
    //#[IsGranted("ROLE_ADMIN")]
    #[Route("/client/{code}/active/{status}", name: "api_admin_patch_client_active_status", methods: ["PATCH"])]
    public function updateActiveStatus($code, bool $status) {
        return $this->manager
                        ->init(["code" => $code])
                        ->bulkUpdateStatuses('active', $status);
    }
}
