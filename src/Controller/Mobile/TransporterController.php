<?php

namespace App\Controller\Mobile;

use App\Manager\TransporterManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use SSH\MyJwtBundle\Annotations\Mapping;

#[Route("/mobile")]
class TransporterController extends AbstractController
{

    private $manager;





    /**
     * TransporterController constructor.
     */
    public function __construct(TransporterManager $manager)
    {
        $this->manager = $manager;
    }



    /**
     * @Route("/transporter", name="api_new_transporter", methods={"POST"})
     * @Mapping(object="App\APIModel\Mobile\Transporter", as="transporter")
     */
    public function createTransporter()
    {
        return $this->manager
                        ->createTransporter();
    }



    #[IsGranted("ROLE_TRANSPORTER")]
    #[Route("/transporter", name: "api_get_transporter", methods: ["GET"])]
    public function getTransporter()
    {
        return $this->manager
                        ->init(["code" => $this->getUser()->getCode()])
                        ->getTransporter(true);
    }



    #[IsGranted("ROLE_TRANSPORTER")]
    /**
     * @Route("/transporter", name="api_update_transporter", methods={"PUT"})
     * @Mapping(object="App\APIModel\Mobile\Transporter", as="_transporter")
     */
    public function editTransporter()
    {
        return $this->manager
                        ->init(["code" => $this->getUser()->getCode()])
                        ->editTransporter();
    }



    #[IsGranted("ROLE_TRANSPORTER")]
    #[Route("/transporter/verified/{status}", name: "api_patch_idVerification_status_tr", methods: ["PATCH"])]
    public function updateIdVerificationStatus(bool $status) {
        return $this->manager
                        ->init(["code" => $this->getUser()->getCode()])
                        ->bulkUpdateStatuses('idVerified', $status);
    }



    #[IsGranted("ROLE_TRANSPORTER")]
    #[Route("/transporter", name: "api_delete_transporter", methods: ["DELETE"])]
    public function deleteTransporter()
    {
        return $this->manager
                        ->init(["code" => $this->getUser()->getCode()])
                        ->deleteTransporter();
    }
}
