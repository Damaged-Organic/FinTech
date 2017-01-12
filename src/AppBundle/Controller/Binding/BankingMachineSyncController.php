<?php
// src/AppBundle/Controller/Binding/BankingMachineSyncController.php
namespace AppBundle\Controller\Binding;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Service\Common\Utility\Exceptions\SearchException,
    AppBundle\Service\Common\Utility\Exceptions\PaginatorException;

use AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Controller\Utility\Traits\ClassOperationsTrait,
    AppBundle\Entity\BankingMachine\BankingMachineSync,
    AppBundle\Entity\BankingMachine\BankingMachine,
    AppBundle\Service\Security\BankingMachineSyncBoundlessAccess;

class BankingMachineSyncController extends Controller implements UserRoleListInterface
{
    use ClassOperationsTrait;

    /** @DI\Inject("request_stack") */
    private $_requestStack;

    /** @DI\Inject("doctrine.orm.entity_manager") */
    private $_manager;

    /** @DI\Inject("app.common.paginator") */
    private $_paginator;

    /** @DI\Inject("app.common.search") */
    private $_search;

    /** @DI\Inject("app.common.entity_results_manager") */
    private $_entityResultsManager;

    /** @DI\Inject("app.security.banking_machine_sync_boundless_access") */
    private $_bankingMachineSyncBoundlessAccess;

    public function showAction($objectClass, $objectId)
    {
        if( !$this->_bankingMachineSyncBoundlessAccess->isGranted(BankingMachineSyncBoundlessAccess::BANKING_MACHINE_SYNC_READ) )
            throw $this->createAccessDeniedException('Access denied');

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new BankingMachine, $objectClass):
                $object = $this->_manager->getRepository('AppBundle:BankingMachine\BankingMachine')->find($objectId);

                if( !$object )
                    throw $this->createNotFoundException("Banking Machine identified by `id` {$objectId} not found");
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        $route          = $this->_requestStack->getMasterRequest()->get('_route');
        $routeArguments = [
            'objectId'    => $objectId,
            'objectClass' => $this->getObjectClassNameLower(new BankingMachineSync)
        ];

        try {
            $this->_entityResultsManager
                ->setPageArgument($this->_paginator->getPageArgument())
                ->setSearchArgument($this->_search->getSearchArgument())
            ;

            $this->_entityResultsManager->setRouteArguments($routeArguments);
        } catch(PaginatorException $ex) {
            throw $this->createNotFoundException('Invalid page argument');
        } catch(SearchException $ex) {
            return $this->redirectToRoute($route, $routeArguments);
        }

        $bankingMachineSyncs = $this->_entityResultsManager->findRecords($object->getBankingMachineSyncs());

        if( $bankingMachineSyncs === FALSE )
            return $this->redirectToRoute($route, $routeArguments);

        return $this->render('AppBundle:Entity/BankingMachineSync/Binding:show.html.twig', [
            'standalone'          => TRUE,
            'bankingMachineSyncs' => $bankingMachineSyncs,
            'object'              => $object
        ]);
    }
}
