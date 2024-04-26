<?php

namespace App\Controller\Mobile;

use App\Manager\ClientManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use SSH\MyJwtBundle\Annotations\Mapping;


#[Route("/mobile")]
class PController extends AbstractController
{

    private $manager;

    /**
     * ClientController constructor.
     */
    public function __construct(ClientManager $manager)
    {
        $this->manager = $manager;
    }



    /**
     * @Route("/client", name="api_new_client", methods={"POST"})
     * @Mapping(object="App\APIModel\Mobile\Client", as="client")
     */
    public function createClient()
    {
        return $this->manager
                        ->createClient();
    }



    #[IsGranted("ROLE_CLIENT")]
    #[Route("/client", name: "api_get_client", methods: ["GET"])]
    public function getClient()
    {
        return $this->manager
                        ->init(["code" => $this->getUser()->getCode()])
                        ->getClient(true);
    }



    #[IsGranted("ROLE_CLIENT")]
    /**
     * @Route("/client", name="api_edit_client", methods={"PUT"})
     * @Mapping(object="App\APIModel\Mobile\Client", as="_client")
     */
    public function editClient()
    {
        return $this->manager
                        ->init(["code" => $this->getUser()->getCode()])
                        ->editClient();
    }



    #[IsGranted("ROLE_CLIENT")]
    #[Route("/client/verified/{status}", name: "api_patch_idVerification_status", methods: ["PATCH"])]
    public function updateIdVerificationStatus(bool $status) {
        return $this->manager
                        ->init(["code" => $this->getUser()->getCode()])
                        ->bulkUpdateStatuses('idVerified', $status);
    }



    #[IsGranted("ROLE_CLIENT")]
    #[Route("/client/awaiting/{status}", name: "api_patch_awaiting_status", methods: ["PATCH"])]
    public function updateAwaitingStatus(bool $status) {
        return $this->manager
                        ->init(["code" => $this->getUser()->getCode()])
                        ->bulkUpdateStatuses('awaitingForDelivery', $status);
    }



    #[IsGranted("ROLE_CLIENT")]
    #[Route("/client", name: "api_delete_client", methods: ["DELETE"])]
    public function deleteClient()
    {
        return $this->manager
                        ->init(["code" => $this->getUser()->getCode()])
                        ->deleteClient();
    }
}
