<?php
// src/AppBundle/Controller/Binding/BankingMachineController.php
namespace AppBundle\Controller\Binding;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Service\Common\Utility\Exceptions\SearchException,
    AppBundle\Service\Common\Utility\Exceptions\PaginatorException;

use AppBundle\Controller\Utility\Traits\EntityFilter,
    AppBundle\Controller\Utility\Traits\ClassOperationsTrait,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

use AppBundle\Entity\BankingMachine\BankingMachine,
    AppBundle\Security\Authorization\Voter\BankingMachineVoter,
    AppBundle\Service\Security\BankingMachineBoundlessAccess;

use AppBundle\Entity\Organization\Organization,
    AppBundle\Security\Authorization\Voter\OrganizationVoter;

use AppBundle\Entity\Operator\Operator;

class BankingMachineController extends Controller implements UserRoleListInterface
{
    use ClassOperationsTrait, EntityFilter;

    /** @DI\Inject("request_stack") */
    private $_requestStack;

    /** @DI\Inject("doctrine.orm.entity_manager") */
    private $_manager;

    /** @DI\Inject("translator") */
    private $_translator;

    /** @DI\Inject("app.common.breadcrumbs") */
    private $_breadcrumbs;

    /** @DI\Inject("app.common.messages") */
    private $_messages;

    /** @DI\Inject("app.common.paginator") */
    private $_paginator;

    /** @DI\Inject("app.common.search") */
    private $_search;

    /** @DI\Inject("app.common.entity_results_manager") */
    private $_entityResultsManager;

    /** @DI\Inject("app.security.banking_machine_boundless_access") */
    private $_bankingMachineBoundlessAccess;

    public function showAction($objectClass, $objectId)
    {
        if( !$this->_bankingMachineBoundlessAccess->isGranted(BankingMachineBoundlessAccess::BANKING_MACHINE_READ) )
            throw $this->createAccessDeniedException('Access denied');

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Organization, $objectClass):
                $object = $this->_manager->getRepository('AppBundle:Organization\Organization')->find($objectId);

                if( !$object )
                    throw $this->createNotFoundException("Organization identified by `id` {$objectId} not found");

                $this->_entityResultsManager->setFindArgument(['organization' => $object]);

                $action = [
                    'path'  => 'banking_machine_choose',
                    'voter' => OrganizationVoter::ORGANIZATION_BIND
                ];
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        $route          = $this->_requestStack->getMasterRequest()->get('_route');
        $routeArguments = [
            'objectId'    => $objectId,
            'objectClass' => $this->getObjectClassNameLower(new BankingMachine)
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

        $bankingMachines = $this->_entityResultsManager->findRecords(
            $this->_manager->getRepository('AppBundle:BankingMachine\BankingMachine')
        );

        if( $bankingMachines === FALSE )
            return $this->redirectToRoute($route, $routeArguments);

        $bankingMachines = $this->filterDeletedIfNotGranted(
            BankingMachineVoter::BANKING_MACHINE_READ, $bankingMachines
        );

        return $this->render('AppBundle:Entity/BankingMachine/Binding:show.html.twig', [
            'standalone'      => TRUE,
            'bankingMachines' => $bankingMachines,
            'object'          => $object,
            'action'          => $action
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/banking_machine/update/{objectId}/bounded/{objectClass}",
     *      name="banking_machine_update_bounded",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "objectId" = "\d+", "objectClass" = "[a-z]+"}
     * )
     */
    public function boundedAction($objectId, $objectClass)
    {
        $bankingMachine = $this->_manager->getRepository('AppBundle:BankingMachine\BankingMachine')->find($objectId);

        if( !$bankingMachine )
            throw $this->createNotFoundException("Banking Machine identified by `id` {$objectId} not found");

        if( !$this->isGranted(BankingMachineVoter::BANKING_MACHINE_READ, $bankingMachine) )
            throw $this->createAccessDeniedException('Access denied');

        $this->_breadcrumbs
            ->add('banking_machine_read')
            ->add('banking_machine_update', [
                'id' => $objectId
            ], $this->_translator->trans('banking_machine_bounded', [], 'routes'))
        ;

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Operator, $objectClass):
                $bounded = $this->forward('AppBundle:Binding\Operator:show', [
                    'objectClass' => $this->getObjectClassName($bankingMachine),
                    'objectId'    => $objectId
                ]);

                $this->_breadcrumbs
                    ->add('banking_machine_update_bounded', [
                        'objectId'    => $objectId,
                        'objectClass' => $objectClass
                    ], $this->_translator->trans('operator_read', [], 'routes'))
                ;
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        return $this->render('AppBundle:Entity/BankingMachine/Binding:bounded.html.twig', [
            'objectClass'    => $objectClass,
            'bounded'        => $bounded->getContent(),
            'bankingMachine' => $bankingMachine
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/banking_machine/choose_for/{objectClass}/{objectId}",
     *      name="banking_machine_choose",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function chooseAction($objectClass, $objectId)
    {
        if( !$this->_bankingMachineBoundlessAccess->isGranted(BankingMachineBoundlessAccess::BANKING_MACHINE_BIND) )
            throw $this->createAccessDeniedException('Access denied');

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Organization, $objectClass):
                $organization = $object = $this->_manager->getRepository('AppBundle:Organization\Organization')->find($objectId);

                if( !$organization )
                    throw $this->createNotFoundException("Organization identified by `id` {$objectId} not found");

                $path = 'organization_update_bounded';

                $this->_breadcrumbs
                    ->add('organization_read')
                    ->add('organization_update', ['id' => $objectId])
                    ->add('organization_update_bounded', [
                        'objectId'    => $objectId,
                        'objectClass' => 'bankingmachine'
                    ], $this->_translator->trans('banking_machine_read', [], 'routes'))
                ;
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        $routeArguments = [
            'objectId'    => $objectId,
            'objectClass' => $objectClass
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
            return $this->redirectToRoute('banking_machine_choose', $routeArguments);
        }

        $bankingMachines = $this->_entityResultsManager->findRecords(
            $this->_manager->getRepository('AppBundle:BankingMachine\BankingMachine')
        );

        if( $bankingMachines === FALSE )
            return $this->redirectToRoute('banking_machine_choose', $routeArguments);

        $bankingMachines = $this->filterDeletedIfNotGranted(
            BankingMachineVoter::BANKING_MACHINE_READ, $bankingMachines
        );

        $this->_breadcrumbs->add('banking_machine_choose', $routeArguments);

        return $this->render('AppBundle:Entity/BankingMachine/Binding:choose.html.twig', [
            'path'            => $path,
            'bankingMachines' => $bankingMachines,
            'object'          => $object
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/banking_machine/bind/{targetId}/{objectClass}/{objectId}",
     *      name="banking_machine_bind",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "targetId" = "\d+", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function bindToAction(Request $request, $targetId, $objectClass, $objectId)
    {
        $bankingMachine = $this->_manager->getRepository('AppBundle:BankingMachine\BankingMachine')->find($targetId);

        if( !$bankingMachine )
            throw $this->createNotFoundException($this->_translator->trans('common.error.not_found', [], 'responses'));

        if( !$this->isGranted(BankingMachineVoter::BANKING_MACHINE_BIND, $bankingMachine) )
            throw $this->createAccessDeniedException($this->_translator->trans('common.error.forbidden', [], 'responses'));

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Organization, $objectClass):
                $organization = $this->_manager->getRepository('AppBundle:Organization\Organization')->find($objectId);

                if( !$organization )
                    throw $this->createNotFoundException($this->_translator->trans('common.error.not_found', [], 'responses'));

                $organization->addBankingMachine($bankingMachine);

                $this->_manager->persist($organization);
            break;

            default:
                throw new NotAcceptableHttpException($this->_translator->trans('bind.error.not_boundalbe', [], 'responses'));
            break;
        }

        $this->_manager->flush();

        $this->_messages->markBindSuccess(
            $this->_translator->trans('bind.success.banking_machine', [], 'responses')
        );

        return new RedirectResponse($request->headers->get('referer'));
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/banking_machine/unbind/{targetId}/{objectClass}/{objectId}",
     *      name="banking_machine_unbind",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "targetId" = "\d+", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function unbindFromAction(Request $request, $targetId, $objectClass, $objectId)
    {
        $bankingMachine = $this->_manager->getRepository('AppBundle:BankingMachine\BankingMachine')->find($targetId);

        if( !$bankingMachine )
            throw $this->createNotFoundException($this->_translator->trans('common.error.not_found', [], 'responses'));

        if( !$this->isGranted(BankingMachineVoter::BANKING_MACHINE_BIND, $bankingMachine) )
            throw $this->createAccessDeniedException($this->_translator->trans('common.error.forbidden', [], 'responses'));

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Organization, $objectClass):
                $bankingMachine->setOrganization(NULL);
            break;

            default:
                throw new NotAcceptableHttpException($this->_translator->trans('bind.error.not_unboundalbe', [], 'responses'));
            break;
        }

        $this->_manager->flush();

        $this->_messages->markUnbindSuccess(
            $this->_translator->trans('unbind.success.banking_machine', [], 'responses')
        );

        return new RedirectResponse($request->headers->get('referer'));
    }
}
