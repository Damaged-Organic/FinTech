<?php
// src/AppBundle/Controller/Binding/OrganizationController.php
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

use AppBundle\Entity\Organization\Organization,
    AppBundle\Security\Authorization\Voter\OrganizationVoter,
    AppBundle\Service\Security\OrganizationBoundlessAccess;

use AppBundle\Entity\Employee\Employee,
    AppBundle\Security\Authorization\Voter\EmployeeVoter;

use AppBundle\Entity\Operator\Operator;

use AppBundle\Entity\BankingMachine\BankingMachine;

use AppBundle\Entity\Account\AccountGroup;

class OrganizationController extends Controller implements UserRoleListInterface
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

    /** @DI\Inject("app.security.organization_boundless_access") */
    private $_organizationBoundlessAccess;

    public function showAction($objectClass, $objectId)
    {
        if( !$this->_organizationBoundlessAccess->isGranted(OrganizationBoundlessAccess::ORGANIZATION_READ) )
            throw $this->createAccessDeniedException('Access denied');

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Employee, $objectClass):
                $object = $this->_manager->getRepository('AppBundle:Employee\Employee')->find($objectId);

                if( !$object )
                    throw $this->createNotFoundException("Employee identified by `id` {$objectId} not found");

                $action = [
                    'path'  => 'organization_choose',
                    'voter' => EmployeeVoter::EMPLOYEE_BIND_ORGANIZATION
                ];
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        $route          = $this->_requestStack->getMasterRequest()->get('_route');
        $routeArguments = [
            'objectId'    => $objectId,
            'objectClass' => $this->getObjectClassNameLower(new Organization)
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

        $organizations = $this->_entityResultsManager->findRecords($object->getOrganizations());

        if( $organizations === FALSE )
            return $this->redirectToRoute($route, $routeArguments);

        $organizations = $this->filterDeletedIfNotGranted(
            OrganizationVoter::ORGANIZATION_READ, $organizations
        );

        return $this->render('AppBundle:Entity/Organization/Binding:show.html.twig', [
            'standalone'    => TRUE,
            'organizations' => $organizations,
            'object'        => $object,
            'action'        => $action
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/organization/update/{objectId}/bounded/{objectClass}",
     *      name="organization_update_bounded",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "objectId" = "\d+", "objectClass" = "[a-z]+"}
     * )
     */
    public function boundedAction($objectId, $objectClass)
    {
        $organization = $this->_manager->getRepository('AppBundle:Organization\Organization')->find($objectId);

        if( !$organization )
            throw $this->createNotFoundException("Organization identified by `id` {$objectId} not found");

        if( !$this->isGranted(OrganizationVoter::ORGANIZATION_READ, $organization) )
            throw $this->createAccessDeniedException('Access denied');

        $this->_breadcrumbs
            ->add('organization_read')
            ->add('organization_update', [
                'id' => $objectId
            ], $this->_translator->trans('organization_bounded', [], 'routes'))
        ;

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Operator, $objectClass):
                $bounded = $this->forward('AppBundle:Binding\Operator:show', [
                    'objectClass' => $this->getObjectClassName($organization),
                    'objectId'    => $objectId
                ]);

                $this->_breadcrumbs
                    ->add('organization_update_bounded', [
                        'objectId'    => $objectId,
                        'objectClass' => $objectClass
                    ], $this->_translator->trans('operator_read', [], 'routes'))
                ;
            break;

            case $this->compareObjectClassNameToString(new BankingMachine, $objectClass):
                $bounded = $this->forward('AppBundle:Binding\BankingMachine:show', [
                    'objectClass' => $this->getObjectClassName($organization),
                    'objectId'    => $objectId
                ]);

                $this->_breadcrumbs
                    ->add('organization_update_bounded', [
                        'objectId'    => $objectId,
                        'objectClass' => $objectClass
                    ], $this->_translator->trans('banking_machine_read', [], 'routes'))
                ;
            break;

            case $this->compareObjectClassNameToString(new AccountGroup, $objectClass):
                $bounded = $this->forward('AppBundle:Binding\AccountGroup:show', [
                    'objectClass' => $this->getObjectClassName($organization),
                    'objectId'    => $objectId
                ]);

                $this->_breadcrumbs
                    ->add('organization_update_bounded', [
                        'objectId'    => $objectId,
                        'objectClass' => $objectClass
                    ], $this->_translator->trans('account_group_read', [], 'routes'))
                ;
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        return $this->render('AppBundle:Entity/Organization/Binding:bounded.html.twig', [
            'objectClass'  => $objectClass,
            'bounded'      => $bounded->getContent(),
            'organization' => $organization
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/organization/choose_for/{objectClass}/{objectId}",
     *      name="organization_choose",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function chooseAction($objectClass, $objectId)
    {
        if( !$this->_organizationBoundlessAccess->isGranted(OrganizationBoundlessAccess::ORGANIZATION_BIND) )
            throw $this->createAccessDeniedException('Access denied');

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Employee, $objectClass):
                $employee = $object = $this->_manager->getRepository('AppBundle:Employee\Employee')->find($objectId);

                if( !$employee )
                    throw $this->createNotFoundException("Employee identified by `id` {$objectId} not found");

                if( !$this->isGranted(EmployeeVoter::EMPLOYEE_BIND_ORGANIZATION, $employee) )
                    throw $this->createAccessDeniedException('Access denied: Schools can be bound to manager only');

                $path = 'employee_update_bounded';

                $this->_breadcrumbs
                    ->add('employee_read')
                    ->add('employee_update', ['id' => $objectId])
                    ->add('employee_update_bounded', [
                        'objectId'    => $objectId,
                        'objectClass' => 'organization'
                    ], $this->_translator->trans('organization_read', [], 'routes'))
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
            return $this->redirectToRoute('organization_choose', $routeArguments);
        }

        $organizations = $this->_entityResultsManager->findRecords(
            $this->_manager->getRepository('AppBundle:Organization\Organization')
        );

        if( $organizations === FALSE )
            return $this->redirectToRoute('organization_choose', $routeArguments);

        $organizations = $this->filterDeletedIfNotGranted(
            OrganizationVoter::ORGANIZATION_READ, $organizations
        );

        $this->_breadcrumbs->add('organization_choose', $routeArguments);

        return $this->render('AppBundle:Entity/Organization/Binding:choose.html.twig', [
            'path'          => $path,
            'organizations' => $organizations,
            'object'        => $object
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/organization/bind/{targetId}/{objectClass}/{objectId}",
     *      name="organization_bind",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "targetId" = "\d+", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function bindToAction(Request $request, $targetId, $objectClass, $objectId)
    {
        $organization = $this->_manager->getRepository('AppBundle:Organization\Organization')->find($targetId);

        if( !$organization )
            throw $this->createNotFoundException($this->_translator->trans('common.error.not_found', [], 'responses'));

        if( !$this->isGranted(OrganizationVoter::ORGANIZATION_BIND, $organization) )
            throw $this->createAccessDeniedException($this->_translator->trans('common.error.forbidden', [], 'responses'));

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Employee, $objectClass):
                $employee = $this->_manager->getRepository('AppBundle:Employee\Employee')->find($objectId);

                if( !$employee )
                    throw $this->createNotFoundException($this->_translator->trans('common.error.not_found', [], 'responses'));

                if( !$this->isGranted(EmployeeVoter::EMPLOYEE_BIND_ORGANIZATION, $employee) )
                    throw $this->createAccessDeniedException($this->_translator->trans('common.error.forbidden', [], 'responses'));

                $employee->addOrganization($organization);

                $this->_manager->persist($employee);
            break;

            default:
                throw new NotAcceptableHttpException($this->_translator->trans('bind.error.not_boundalbe', [], 'responses'));
            break;
        }

        $this->_manager->flush();

        $this->_messages->markBindSuccess(
            $this->_translator->trans('bind.success.organization', [], 'responses')
        );

        return new RedirectResponse($request->headers->get('referer'));
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/organization/unbind/{targetId}/{objectClass}/{objectId}",
     *      name="organization_unbind",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "targetId" = "\d+", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function unbindFromAction(Request $request, $targetId, $objectClass, $objectId)
    {
        $organization = $this->_manager->getRepository('AppBundle:Organization\Organization')->find($targetId);

        if( !$organization )
            throw $this->createNotFoundException($this->_translator->trans('common.error.not_found', [], 'responses'));

        if( !$this->isGranted(OrganizationVoter::ORGANIZATION_BIND, $organization) )
            throw $this->createAccessDeniedException($this->_translator->trans('common.error.forbidden', [], 'responses'));

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Employee, $objectClass):
                $employee = $this->_manager->getRepository('AppBundle:Employee\Employee')->find($objectId);

                if( !$employee )
                    throw $this->createNotFoundException($this->_translator->trans('common.error.not_found', [], 'responses'));

                $employee->removeOrganization($organization);

                $this->_manager->persist($employee);
            break;

            default:
                throw new NotAcceptableHttpException($this->_translator->trans('bind.error.not_unboundalbe', [], 'responses'));
            break;
        }

        $this->_manager->flush();

        $this->_messages->markUnbindSuccess(
            $this->_translator->trans('unbind.success.organization', [], 'responses')
        );

        return new RedirectResponse($request->headers->get('referer'));
    }
}
