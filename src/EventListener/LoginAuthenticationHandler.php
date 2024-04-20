<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use App\Manager\AbstractManager;
use App\Manager\ClientManager;
use App\Manager\TransporterManager;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

class LoginAuthenticationHandler //implements AuthenticationSuccessHandlerInterface
{
    private $clientManager;
    private $transporterManager;

    /**
     *
     * @var RoleHierarchyInterface
     */
    private $roleHierarchy;

    /**
     * CurrentUserSubscriber constructor.
     */
    public function __construct(
            ClientManager $clientManager,
            TransporterManager $transporterManager,
            RoleHierarchyInterface $roleHierarchy
    )
    {
        $this->roleHierarchy = $roleHierarchy;
        $this->clientManager = $clientManager;
        $this->transporterManager = $transporterManager;
    }

    private function getRoles($role)
    {
        $prefix = 'ROLE_';
        $roles = $this->roleHierarchy->getReachableRoleNames([$prefix . $role]);

        foreach ($roles as &$role) {
            $role = str_replace($prefix, '', $role);
        }

        return $roles;
    }

    /**
     * Handle the output from the controllers.
     *
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(ViewEvent $event)
    {
        $result = $event->getControllerResult();
        $request = $event->getRequest();
        $route = $request->get('_route');

        $informations = [];

        if ($route == 'jwt_loginpassword_authenticate') {
            $code = $result['user']['code'];
            $roles = $result['user']['roles'];
            unset($result['user']);

            if (in_array(AbstractManager::ROLE_TRANSPORTER, $roles)) {

                $informations = $this->transporterManager
                        ->init(['code' => $code])
                        ->getTransporter(true);

            }

            if (in_array(AbstractManager::ROLE_CLIENT, $roles)) {
                $informations = $this->clientManager
                        ->init(['code' => $code])
                        ->getClient(true);
            }
        }
        $event->setControllerResult($informations + $result);
    }
}
