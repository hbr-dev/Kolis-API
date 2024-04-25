<?php

namespace App\Manager;

use App\Entity\Pack;
use App\Entity\Subscription;
use App\Entity\Transporter;
use App\Manager\AbstractManager;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use SSH\MyJwtBundle\Manager\ExceptionManager;
use Symfony\Component\HttpFoundation\RequestStack;


class SubscriptionManager extends AbstractManager
{
    /**
     * @var Pack
     */
    private $pack;

    /**
     * @var Subscription
     */
    private $subscription;

    /**
     * @var Transporter
     */
    private $transporter;

    /**
     * @var ExceptionManager
     */
    private $exceptionManager;

    /**
     * @var TransporterManager
     */
    private $transporterManager;

    /**
     * @var PackManager
     */
    private $packManager;

    private $transporterCode;
    private $packCode;
    private $code;
    private $em;




    public function __construct(TransporterManager $transporterManager, PackManager $packManager, RequestStack $requestStack, ExceptionManager $exceptionManager, EntityManagerInterface $em) 
    {
        $this->transporterManager = $transporterManager;
        $this->packManager = $packManager;
        $this->exceptionManager = $exceptionManager;
        $this->em = $em;

        parent::__construct($requestStack, $em);
    }    
    

    
    /**
     * Initializes the Pack
     * object, and its parent
     * object.
     *
     * @param array $settings
     */
    public function init($settings = [])
    {
        parent::setSettings($settings);

        if ($this->getCode()) {
            $filters = ['code' => $this->getCode()];
           
            $this->subscription = $this->em
                                    ->getRepository(Subscription::class)
                                    ->findOneBy($filters);

            if (empty($this->subscription)) {
                $this->exceptionManager->throwNotFoundException('NO_SUBSCRIPTION_FOUND');
            }
        }

        if ($this->getTransporterCode()) {
            $this->transporter = $this->transporterManager
                                        ->init(['code' => $this->getTransporterCode()])
                                        ->getTransporter();
        }

        if ($this->getPackCode()) {
            $this->pack = $this->packManager
                                        ->init(['code' => $this->getPackCode()])
                                        ->getPack();
        }

        return $this;
    }



    public function getCode() 
    {
        return $this->code;
    }



    public function setCode($code) 
    {
        $this->code = $code;
        return $this;

    }



    public function getTransporterCode() 
    {
        return $this->transporterCode;
    }



    public function setTransporterCode($transporterCode) 
    {
        $this->transporterCode = $transporterCode;
        return $this;

    }



    public function getPackCode() 
    {
        return $this->packCode;
    }



    public function setPackCode($packCode) 
    {
        $this->packCode = $packCode;
        return $this;

    }



    public function createSubscription()
    {
        $subscriptions = $this->getTransporterSubscriptions(returnResponse: false);

        if ( count($subscriptions) == 0 || (count($subscriptions) != 0 && $this->noActiveSubscription($subscriptions)) ) {
            $data = (array) $this->request->get('subscription');
            $data['transporter'] = $this->transporter;
            $data['pack'] = $this->pack;
    
            $currentDate = new \DateTime();
            $expirationDate = $currentDate->modify('+' . $this->pack->getSubscriptionPeriod() . ' days')
                                          ->modify('+' . $this->pack->getFreeTrialPeriod() . ' days');
    
            $expirationDate->setTimezone(new \DateTimeZone('CET'));
    
            $data['expiration_date'] = $expirationDate;
    
            $subscription = $this->insertObject($data, Subscription::class);
    
            return ['data' => [
                    'messages' => 'create_success',
                    'object' => $subscription->getCode()
            ]];
        } else {
            return [
                'data' => 'You steal have an active subscription'
            ];
        }
    }



    private function noActiveSubscription(array $subscriptions): bool
    {
        foreach ($subscriptions as $subscription) {
            if ( $subscription->getExpirationDate() > new \DateTime('now', new \DateTimeZone('CET')) ) {
                return false;
            }
        }

        return true;
    }



    public function getTransporterSubscriptions($returnResponse = true)
    {
        $subscriptionsAsObjects = $this->getObjectsByOwner("Subscription", "transporter", $this->transporter);
        
        if ($returnResponse) {
            $subscriptions = [];
    
            foreach ($subscriptionsAsObjects as $object) {
                $subscriptions[$object->getId()] = $object->toArray();
            }
    
            return ['data' => $subscriptions];
        }

        return $subscriptionsAsObjects;
    }
    

    
    public function renewSubscription()
    {        
        if ($this->subscription->getExpirationDate() < new \DateTime('now', new \DateTimeZone('CET'))) {
            $data = (array) $this->request->get("my_subscription");
            
            $currentDate = new \DateTime();
            $expirationDate = $currentDate->modify('+' . $this->subscription->getPack()->getSubscriptionPeriod() . ' days');
    
            $expirationDate->setTimezone(new \DateTimeZone('CET'));
    
            $data['expiration_date'] = $expirationDate;
    
            return $this->updateObject(Subscription::class, $this->subscription, $data);
        } else {
            return [
                'data' => 'Your subscription plan is active until ' . $this->subscription->getExpirationDate()->format('Y-m-d H:i')
            ];
        }
    }



    public function cancelSubscription() 
    {
        return $this->deleteObject($this->subscription);
    }
}
