<?php
// src/AppBundle/Controller/Binding/AccountGroupController.php
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

use AppBundle\Entity\Account\AccountGroup,
    AppBundle\Security\Authorization\Voter\AccountGroupVoter,
    AppBundle\Service\Security\AccountGroupBoundlessAccess;

use AppBundle\Entity\Organization\Organization,
    AppBundle\Security\Authorization\Voter\OrganizationVoter;

use AppBundle\Entity\Account\Account;

class AccountGroupController extends Controller implements UserRoleListInterface
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

    /** @DI\Inject("app.security.account_group_boundless_access") */
    private $_accountGroupBoundlessAccess;

    public function showAction($objectClass, $objectId)
    {
        if( !$this->_accountGroupBoundlessAccess->isGranted(AccountGroupBoundlessAccess::ACCOUNT_GROUP_READ) )
            throw $this->createAccessDeniedException('Access denied');

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Organization, $objectClass):
                $object = $this->_manager->getRepository('AppBundle:Organization\Organization')->find($objectId);

                if( !$object )
                    throw $this->createNotFoundException("Organization identified by `id` {$objectId} not found");

                $this->_entityResultsManager->setFindArgument(['organization' => $object]);

                $action = [
                    'path'  => 'account_group_choose',
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
            'objectClass' => $this->getObjectClassNameLower(new AccountGroup)
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

        $accountGroups = $this->_entityResultsManager->findRecords(
            $this->_manager->getRepository('AppBundle:Account\AccountGroup')
        );

        if( $accountGroups === FALSE )
            return $this->redirectToRoute($route, $routeArguments);

        $accountGroups = $this->filterDeletedIfNotGranted(
            AccountGroupVoter::ACCOUNT_GROUP_READ, $accountGroups
        );

        return $this->render('AppBundle:Entity/AccountGroup/Binding:show.html.twig', [
            'standalone'    => TRUE,
            'accountGroups' => $accountGroups,
            'object'        => $object,
            'action'        => $action
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/account_group/update/{objectId}/bounded/{objectClass}",
     *      name="account_group_update_bounded",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "objectId" = "\d+", "objectClass" = "[a-z]+"}
     * )
     */
    public function boundedAction($objectId, $objectClass)
    {
        $accountGroup = $this->_manager->getRepository('AppBundle:Account\AccountGroup')->find($objectId);

        if( !$accountGroup )
            throw $this->createNotFoundException("Account Group identified by `id` {$objectId} not found");

        if( !$this->isGranted(AccountGroupVoter::ACCOUNT_GROUP_READ, $accountGroup) )
            throw $this->createAccessDeniedException('Access denied');

        $this->_breadcrumbs
            ->add('account_group_read')
            ->add('account_group_update', [
                'id' => $objectId
            ], $this->_translator->trans('account_group_bounded', [], 'routes'))
        ;

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Account, $objectClass):
                $bounded = $this->forward('AppBundle:Binding\Account:show', [
                    'objectClass' => $this->getObjectClassName($accountGroup),
                    'objectId'    => $objectId
                ]);

                $this->_breadcrumbs
                    ->add('account_group_update_bounded', [
                        'objectId'    => $objectId,
                        'objectClass' => $objectClass
                    ], $this->_translator->trans('account_read', [], 'routes'))
                ;
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        return $this->render('AppBundle:Entity/AccountGroup/Binding:bounded.html.twig', [
            'objectClass'  => $objectClass,
            'bounded'      => $bounded->getContent(),
            'accountGroup' => $accountGroup
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/account_group/choose_for/{objectClass}/{objectId}",
     *      name="account_group_choose",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function chooseAction($objectClass, $objectId)
    {
        if( !$this->_accountGroupBoundlessAccess->isGranted(AccountGroupBoundlessAccess::ACCOUNT_GROUP_READ) )
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
                        'objectClass' => 'accountgroup'
                    ], $this->_translator->trans('account_group_read', [], 'routes'))
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
            return $this->redirectToRoute('account_group_choose', $routeArguments);
        }

        $accountGroups = $this->_entityResultsManager->findRecords(
            $this->_manager->getRepository('AppBundle:Account\AccountGroup')
        );

        if( $accountGroups === FALSE )
            return $this->redirectToRoute('account_group_choose', $routeArguments);

        $accountGroups = $this->filterDeletedIfNotGranted(
            AccountGroupVoter::ACCOUNT_GROUP_READ, $accountGroups
        );

        $this->_breadcrumbs->add('account_group_choose', $routeArguments);

        return $this->render('AppBundle:Entity/AccountGroup/Binding:choose.html.twig', [
            'path'          => $path,
            'accountGroups' => $accountGroups,
            'object'        => $object
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/account_group/bind/{targetId}/{objectClass}/{objectId}",
     *      name="account_group_bind",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "targetId" = "\d+", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function bindToAction(Request $request, $targetId, $objectClass, $objectId)
    {
        $accountGroup = $this->_manager->getRepository('AppBundle:Account\AccountGroup')->find($targetId);

        if( !$accountGroup )
            throw $this->createNotFoundException($this->_translator->trans('common.error.not_found', [], 'responses'));

        if( !$this->isGranted(AccountGroupVoter::ACCOUNT_GROUP_BIND, $accountGroup) )
            throw $this->createAccessDeniedException($this->_translator->trans('common.error.forbidden', [], 'responses'));

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Organization, $objectClass):
                $organization = $this->_manager->getRepository('AppBundle:Organization\Organization')->find($objectId);

                if( !$organization )
                    throw $this->createNotFoundException($this->_translator->trans('common.error.not_found', [], 'responses'));

                $organization->addAccountGroup($accountGroup);

                $this->_manager->persist($organization);
            break;

            default:
                throw new NotAcceptableHttpException($this->_translator->trans('bind.error.not_boundalbe', [], 'responses'));
            break;
        }

        $this->_manager->flush();

        $this->_messages->markBindSuccess(
            $this->_translator->trans('bind.success.account_group', [], 'responses')
        );

        return new RedirectResponse($request->headers->get('referer'));
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/account_group/unbind/{targetId}/{objectClass}/{objectId}",
     *      name="account_group_unbind",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "targetId" = "\d+", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function unbindFromAction(Request $request, $targetId, $objectClass, $objectId)
    {
        $accountGroup = $this->_manager->getRepository('AppBundle:Account\AccountGroup')->find($targetId);

        if( !$accountGroup )
            throw $this->createNotFoundException($this->_translator->trans('common.error.not_found', [], 'responses'));

        if( !$this->isGranted(AccountGroupVoter::ACCOUNT_GROUP_BIND, $accountGroup) )
            throw $this->createAccessDeniedException($this->_translator->trans('common.error.forbidden', [], 'responses'));

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Organization, $objectClass):
                $accountGroup->setOrganization(NULL);
            break;

            default:
                throw new NotAcceptableHttpException($this->_translator->trans('bind.error.not_unboundalbe', [], 'responses'));
            break;
        }

        $this->_manager->flush();

        $this->_messages->markUnbindSuccess(
            $this->_translator->trans('unbind.success.account_group', [], 'responses')
        );

        return new RedirectResponse($request->headers->get('referer'));
    }
}
