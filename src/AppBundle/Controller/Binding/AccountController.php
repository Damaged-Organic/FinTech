<?php
// src/AppBundle/Controller/Binding/AccountController.php
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

use AppBundle\Entity\Account\Account,
    AppBundle\Security\Authorization\Voter\AccountVoter,
    AppBundle\Service\Security\AccountBoundlessAccess;

use AppBundle\Entity\Account\AccountGroup,
    AppBundle\Security\Authorization\Voter\AccountGroupVoter;

class AccountController extends Controller implements UserRoleListInterface
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

    /** @DI\Inject("app.security.account_boundless_access") */
    private $_accountBoundlessAccess;

    public function showAction($objectClass, $objectId)
    {
        if( !$this->_accountBoundlessAccess->isGranted(AccountBoundlessAccess::ACCOUNT_READ) )
            throw $this->createAccessDeniedException('Access denied');

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new AccountGroup, $objectClass):
                $object = $this->_manager->getRepository('AppBundle:Account\AccountGroup')->find($objectId);

                if( !$object )
                    throw $this->createNotFoundException("Account Group identified by `id` {$objectId} not found");

                $action = [
                    'path'  => 'account_choose',
                    'voter' => AccountGroupVoter::ACCOUNT_GROUP_BIND
                ];
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        $route          = $this->_requestStack->getMasterRequest()->get('_route');
        $routeArguments = [
            'objectId'    => $objectId,
            'objectClass' => $this->getObjectClassNameLower(new Account)
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

        $accounts = $this->_entityResultsManager->findRecords($object->getAccounts());

        if( $accounts === FALSE )
            return $this->redirectToRoute($route, $routeArguments);

        $accounts = $this->filterUnlessGranted(
            AccountVoter::ACCOUNT_READ, $accounts
        );

        return $this->render('AppBundle:Entity/Account/Binding:show.html.twig', [
            'standalone' => TRUE,
            'accounts'   => $accounts,
            'object'     => $object,
            'action'     => $action
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/account/choose_for/{objectClass}/{objectId}",
     *      name="account_choose",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function chooseAction($objectClass, $objectId)
    {
        if( !$this->_accountBoundlessAccess->isGranted(AccountBoundlessAccess::ACCOUNT_BIND) )
            throw $this->createAccessDeniedException('Access denied');

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new AccountGroup, $objectClass):
                $organization = $object = $this->_manager->getRepository('AppBundle:Account\AccountGroup')->find($objectId);

                if( !$organization )
                    throw $this->createNotFoundException("Account Group identified by `id` {$objectId} not found");

                $path = 'account_group_update_bounded';

                $this->_breadcrumbs
                    ->add('account_group_read')
                    ->add('account_group_update', ['id' => $objectId])
                    ->add('account_group_update_bounded', [
                        'objectId'    => $objectId,
                        'objectClass' => 'account'
                    ], $this->_translator->trans('account_read', [], 'routes'))
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
            return $this->redirectToRoute('account_choose', $routeArguments);
        }

        $accounts = $this->_entityResultsManager->findRecords(
            $this->_manager->getRepository('AppBundle:Account\Account')
        );

        if( $accounts === FALSE )
            return $this->redirectToRoute('account_choose', $routeArguments);

        $accounts = $this->filterUnlessGranted(
            AccountVoter::ACCOUNT_READ, $accounts
        );

        $this->_breadcrumbs->add('account_choose', $routeArguments);

        return $this->render('AppBundle:Entity/Account/Binding:choose.html.twig', [
            'path'     => $path,
            'accounts' => $accounts,
            'object'   => $object
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/account/bind/{targetId}/{objectClass}/{objectId}",
     *      name="account_bind",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "targetId" = "\d+", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function bindToAction(Request $request, $targetId, $objectClass, $objectId)
    {
        $account = $this->_manager->getRepository('AppBundle:Account\Account')->find($targetId);

        if( !$account )
            throw $this->createNotFoundException($this->_translator->trans('common.error.not_found', [], 'responses'));

        if( !$this->isGranted(AccountVoter::ACCOUNT_BIND, $account) )
            throw $this->createAccessDeniedException($this->_translator->trans('common.error.forbidden', [], 'responses'));

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new AccountGroup, $objectClass):
                $accountGroup = $this->_manager->getRepository('AppBundle:Account\AccountGroup')->find($objectId);

                if( !$accountGroup )
                    throw $this->createNotFoundException($this->_translator->trans('common.error.not_found', [], 'responses'));

                $accountGroup->addAccount($account);

                $this->_manager->persist($accountGroup);
            break;

            default:
                throw new NotAcceptableHttpException($this->_translator->trans('bind.error.not_boundalbe', [], 'responses'));
            break;
        }

        $this->_manager->flush();

        $this->_messages->markBindSuccess(
            $this->_translator->trans('bind.success.account', [], 'responses')
        );

        return new RedirectResponse($request->headers->get('referer'));
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/account/unbind/{targetId}/{objectClass}/{objectId}",
     *      name="account_unbind",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "targetId" = "\d+", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function unbindFromAction(Request $request, $targetId, $objectClass, $objectId)
    {
        $account = $this->_manager->getRepository('AppBundle:Account\Account')->find($targetId);

        if( !$account )
            throw $this->createNotFoundException($this->_translator->trans('common.error.not_found', [], 'responses'));

        if( !$this->isGranted(AccountVoter::ACCOUNT_BIND, $account) )
            throw $this->createAccessDeniedException($this->_translator->trans('common.error.forbidden', [], 'responses'));

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new AccountGroup, $objectClass):
                $account->setAccountGroup(NULL);
            break;

            default:
                throw new NotAcceptableHttpException($this->_translator->trans('bind.error.not_unboundable', [], 'responses'));
            break;
        }

        $this->_manager->flush();

        $this->_messages->markUnbindSuccess(
            $this->_translator->trans('unbind.success.account', [], 'responses')
        );

        return new RedirectResponse($request->headers->get('referer'));
    }
}
