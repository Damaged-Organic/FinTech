<?php
// src/SyncBundle/EventListener/Security/AuthorizationListener.php
namespace SyncBundle\EventListener\Security;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException,
    Symfony\Component\HttpKernel\Exception\NotFoundHttpException,
    Symfony\Component\HttpKernel\Event\FilterControllerEvent;

use Doctrine\ORM\EntityManager;

use SyncBundle\Service\BankingMachine\Security\Authorization,
    SyncBundle\Controller\VersionOne\Utility\Interfaces\Markers\AuthorizationMarkerInterface;

class AuthorizationListener
{
    private $_manager;
    private $_authorization;

    public function setManager(EntityManager $manager)
    {
        $this->_manager = $manager;
    }

    public function setAuthorization(Authorization $authorization)
    {
        $this->_authorization = $authorization;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $request    = $event->getRequest();
        $controller = $event->getController();

        if( !is_array($controller) )
            return;

        if ($controller[0] instanceof AuthorizationMarkerInterface)
        {
            $serial = $request->attributes->get('_route_params')['serial'];

            // $vendingMachine = $this->_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->findOneBy([
            //     'serial' => $serial
            // ]);
            //
            // if( !$vendingMachine )
            //     throw new NotFoundHttpException('Vending Machine not found');
            //
            // if( !$this->_authentication->authenticate($request, $vendingMachine) )
            //     throw new AccessDeniedHttpException('Authentication failed');
        }
    }
}
