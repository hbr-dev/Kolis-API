<?php

namespace App\Controller\Back;

use App\Manager\SubscriptionManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/back")]
class SubscriptionController extends AbstractController
{

    private $manager;

    /**
     * Admin VehicleController constructor.
     */
    public function __construct(SubscriptionManager $manager) {
        $this->manager = $manager;
    }



    #[IsGranted("ROLE_ADMIN")]
    #[Route("/subscriptions", name: "api_admin_get_subscriptions", methods: ["GET"])]
    public function getSubscriptions()
    {
        return $this->manager
                        ->getSubscriptions();
    }
}
