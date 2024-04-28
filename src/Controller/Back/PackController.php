<?php

namespace App\Controller\Back;

use App\Manager\PackManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use SSH\MyJwtBundle\Annotations\Mapping;

#[Route("/back")]
class PackController extends AbstractController
{

    private $manager;

    /**
     * ClientController constructor.
     */
    public function __construct(PackManager $manager) {
        $this->manager = $manager;
    }



    #[IsGranted("ROLE_ADMIN")]
    #[Route("/pack/{code}", name: "api_get_pack", methods: ["GET"])]
    public function getPack($code)
    {
        return $this->manager
                        ->init(['code' => $code])
                        ->getPack(true);
    }



    #[IsGranted("ROLE_ADMIN")]
    #[Route("/packs", name: "api_admin_get_all_packs", methods: ["GET"])]
    public function getAllPacks()
    {
        return $this->manager
                        ->getAllPacks();
    }



    #[IsGranted("ROLE_ADMIN")]
    /**
     * @Route("/pack", name="api_new_pack", methods={"POST"})
     * @Mapping(object="App\APIModel\Back\Pack", as="pack")
     */
    public function createPack()
    {
        return $this->manager
                        ->createPack();
    }
}