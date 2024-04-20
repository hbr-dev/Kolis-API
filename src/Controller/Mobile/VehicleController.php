<?php

namespace App\Controller\Mobile;

use App\Entity\Vehicle;
use App\Manager\VehicleManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use SSH\MyJwtBundle\Annotations\Mapping;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

#[Route("/mobile")]
class VehicleController extends AbstractController
{

    private $manager;

    /**
     * TransporterController constructor.
     */
    public function __construct(VehicleManager $manager) {
        $this->manager = $manager;
    }



    #[IsGranted("ROLE_TRANSPORTER")]
    /**
     * @Route("/vehicle", name="api_new_vehile", methods={"POST"})
     * @Mapping(object="App\APIModel\Mobile\Vehicle", as="vehicle")
     */
    public function createVehicle()
    {
        return $this->manager
                        ->init(['transporterCode' => $this->getUser()->getCode()])
                        ->createVehicle();
    }



    #[IsGranted("ROLE_TRANSPORTER")]
    #[Route("/vehicle/{code}", name: "api_get_vehicle", methods: ["GET"])]
    public function getVehile($code, Vehicle $vehicle, AuthorizationCheckerInterface $authorizationChecker) 
    {
        
        if (!$authorizationChecker->isGranted('VIEW', $vehicle)) {
            throw new AccessDeniedHttpException('Access denied.');
        }

        return $this->manager
                        ->init(['code' => $code])
                        ->getVehicle(true);
    }



    #[IsGranted("ROLE_TRANSPORTER")]
    #[Route("/vehicle/{code}", name: "api_delete_vehile", methods: ["DELETE"])]
    public function deleteVehicle($code, Vehicle $vehicle, AuthorizationCheckerInterface $authorizationChecker) {
        
        if (!$authorizationChecker->isGranted('VIEW', $vehicle)) {
            throw new AccessDeniedHttpException('Access denied.');
        }

        return $this->manager
                        ->init(['code' => $code])
                        ->deleteVehicle();
    }
}
