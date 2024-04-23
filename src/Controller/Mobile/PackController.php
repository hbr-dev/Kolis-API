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
                    ->init(['transporterCode' => $this->getUser()->getCode()])
                    ->createPack();
    }



    #[IsGranted("ROLE_TRANSPORTER")]
    #[Route("/pack/{code}", name: "api_get_pack", methods: ["GET"])]
    public function getPack($code, Pack $pack, AuthorizationCheckerInterface $authorizationChecker) 
    {
        // To Prevent conncected to get others packs
        if (!$authorizationChecker->isGranted('VIEW', $pack)) {
            throw new AccessDeniedHttpException('Access denied.');
        }
        
        return $this->manager
                        ->init(['code' => $code])
                        ->getPack(true);
    }



    #[IsGranted('ROLE_TRANSPORTER')]
    #[Route("/packs", name: "api_get_my_packs", methods: ["GET"])]
    public function getTransporterPacks()
    {
        return $this->manager
                        ->init(['transporterCode'=>$this->getUser()->getCode()])
                        ->getTransporterPacks();
    }



    #[IsGranted("ROLE_TRANSPORTER")]
    #[Route("/pack/{code}", name: "api_delete_pack", methods: ["DELETE"])]
    public function cancelSubscription($code, Pack $pack, AuthorizationCheckerInterface $authorizationChecker) 
    {
        
        if (!$authorizationChecker->isGranted('VIEW', $pack)) {
            throw new AccessDeniedHttpException('Access denied.');
        }
    
        return $this->manager
                        ->init(['code' => $code])
                        ->cancelSubscription();
    }



    #[IsGranted("ROLE_TRANSPORTER")]
     /**
     * @Route("/pack/{code}", name="api_update_pack", methods={"POST"})
     * @Mapping(object="App\APIModel\Mobile\Pack", as="_pack")
     */
    public function updateSubscription($code, Pack $pack, AuthorizationCheckerInterface $authorizationChecker)
    {
        
        if (!$authorizationChecker->isGranted('VIEW', $pack)) {
            throw new AccessDeniedHttpException('Access denied.');
        }
    
        return $this->manager
                        ->init(['code' => $code])
                        ->updateSubscription();
    }
}
