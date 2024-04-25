<?php

namespace App\Controller\Mobile;

use App\Entity\Subscription;
use App\Manager\SubscriptionManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

#[Route("/mobile")]
class SubscriptionController extends AbstractController
{

    private $manager;

    /**
     * ClientController constructor.
     */
    public function __construct(SubscriptionManager $manager) {
        $this->manager = $manager;
    }



    #[IsGranted("ROLE_TRANSPORTER")]
    #[Route("/subscribe/{packCode}", name:"api_subscribe", methods:["POST"])]
    public function subscribe($packCode)
    {
        return $this->manager
                    ->init([
                        'transporterCode' => $this->getUser()->getCode(),
                        'packCode' => $packCode
                    ])
                    ->createSubscription();
    }



    #[IsGranted('ROLE_TRANSPORTER')]
    #[Route("/subscriptions", name: "api_get_my_subscriptions", methods: ["GET"])]
    public function getTransporterSubscriptions()
    {
        return $this->manager
                        ->init(['transporterCode'=>$this->getUser()->getCode()])
                        ->getTransporterSubscriptions();
    }



    #[IsGranted("ROLE_TRANSPORTER")]
    #[Route("/subscribe/{code}", name: "api_cancel_subscription", methods: ["DELETE"])]
    public function cancelSubscription($code, Subscription $subscription, AuthorizationCheckerInterface $authorizationChecker) 
    {
        
        if (!$authorizationChecker->isGranted('VIEW', $subscription)) {
            throw new AccessDeniedHttpException('Access denied.');
        }
    
        return $this->manager
                        ->init(['code' => $code])
                        ->cancelSubscription();
    }



    #[IsGranted("ROLE_TRANSPORTER")]
    #[Route("/subscription/{code}", name:"api_renew_subscription", methods:["POST"])]
    public function renewSubscription($code, Subscription $subscription, AuthorizationCheckerInterface $authorizationChecker)
    {
        
        if (!$authorizationChecker->isGranted('VIEW', $subscription)) {
            throw new AccessDeniedHttpException('Access denied.');
        }
    
        return $this->manager
                        ->init(['code' => $code])
                        ->renewSubscription();
    }
}
