<?php
// src/SyncBundle/EventListener/Security/AuthorizationListener.php
namespace SyncBundle\EventListener\Security;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException,
    Symfony\Component\HttpKernel\Exception\NotFoundHttpException,
    Symfony\Component\HttpKernel\Event\FilterControllerEvent;

use Doctrine\ORM\EntityManager;

use SyncBundle\EventListener\Utility\Interfaces\AuthorizationMarkerInterface,
    SyncBundle\Service\BankingMachine\Security\Authorization;

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

            $repository = $this->_manager->getRepository('AppBundle:BankingMachine\BankingMachine');

            $bankingMachine = $repository->findOneBy(['serial' => $serial]);

            if( !$bankingMachine )
                throw new NotFoundHttpException('Banking Machine not found');

            if( !$this->_authentication->isAuthorized($request, $bankingMachine) )
                throw new AccessDeniedHttpException('Authentication failed');
        }
    }
}
