<?php

namespace App\Controller\Mobile;

use App\Entity\Pack;
use App\Manager\PackManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use SSH\MyJwtBundle\Annotations\Mapping;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

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
    /**
     * @Route("/pack", name="api_new_pack", methods={"POST"})
     * @Mapping(object="App\APIModel\Mobile\Pack", as="pack")
     */
    public function createPack()
    {
        return $this->manager
                    ->createPack();
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
