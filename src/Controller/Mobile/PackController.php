<?php

namespace App\Controller\Mobile;

use App\Manager\PackManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/mobile")]
class PackController extends AbstractController
{

    private $manager;

    /**
     * ClientController constructor.
     */
    public function __construct(PackManager $manager) {
        $this->manager = $manager;
    }



    #[IsGranted("ROLE_TRANSPORTER")]
    #[Route("/pack/{code}", name: "api_get_pack", methods: ["GET"])]
    public function getPack($code)
    {
        return $this->manager
                        ->init(['code' => $code])
                        ->getPack(true);
    }



    #[IsGranted("ROLE_TRANSPORTER")]
    #[Route("/packs", name: "api_get_all_packs", methods: ["GET"])]
    public function getAllPacks()
    {
        return $this->manager
                        ->getAllPacks();
    }
}
