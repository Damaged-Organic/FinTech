<?php
// src/AppBundle/Controller/CRUD/AccountController.php
namespace AppBundle\Controller\CRUD;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\RedirectResponse;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Service\Common\Utility\Exceptions\SearchException,
    AppBundle\Service\Common\Utility\Exceptions\PaginatorException;

use AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Entity\Account\Account,
    AppBundle\Form\Type\AccountType,
    AppBundle\Security\Authorization\Voter\AccountVoter,
    AppBundle\Service\Security\AccountBoundlessAccess;

class AccountController extends Controller implements UserRoleListInterface
{
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

    /**
     * @Method({"GET"})
     * @Route(
     *      "/account/{id}",
     *      name="account_read",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "id" = null},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function readAction($id = NULL)
    {
        $repository = $this->_manager->getRepository('AppBundle:Account\Account');

        if( $id )
        {
            $account = $repository->find($id);

            if( !$account )
                throw $this->createNotFoundException("Account identified by `id` {$id} not found");

            if( !$this->isGranted(AccountVoter::ACCOUNT_READ, $account) )
                throw $this->createAccessDeniedException('Access denied');

            $response = [
                'view' => 'AppBundle:Entity/Account/CRUD:readItem.html.twig',
                'data' => ['account' => $account]
            ];

            $this->_breadcrumbs
                ->add('account_read')
                ->add('account_read', ['id' => $id], $this->_translator->trans('account_view', [], 'routes'))
            ;
        } else {
            if( !$this->_accountBoundlessAccess->isGranted(AccountBoundlessAccess::ACCOUNT_READ) )
                throw $this->createAccessDeniedException('Access denied');

            try {
                $this->_entityResultsManager
                    ->setPageArgument($this->_paginator->getPageArgument())
                    ->setSearchArgument($this->_search->getSearchArgument())
                ;
            } catch(PaginatorException $ex) {
                throw $this->createNotFoundException('Invalid page argument');
            } catch(SearchException $ex) {
                return $this->redirectToRoute('account_read');
            }

            $accounts = $this->_entityResultsManager->findRecords($repository);

            if( $accounts === FALSE )
                return $this->redirectToRoute('account_read');

            $response = [
                'view' => 'AppBundle:Entity/Account/CRUD:readList.html.twig',
                'data' => ['accounts' => $accounts]
            ];

            $this->_breadcrumbs->add('account_read');
        }

        return $this->render($response['view'], $response['data']);
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/account/create",
     *      name="account_create",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"}
     * )
     */
    public function createAction(Request $request)
    {
        if( !$this->_accountBoundlessAccess->isGranted(AccountBoundlessAccess::ACCOUNT_READ) )
            throw $this->createAccessDeniedException('Access denied');

        $accountType = new AccountType(
            $this->_translator,
            $this->_accountBoundlessAccess->isGranted(AccountBoundlessAccess::ACCOUNT_READ)
        );

        $form = $this->createForm($accountType, $account = new Account, [
            'action' => $this->generateUrl('account_create')
        ]);

        $form->handleRequest($request);

        if( $form->isSubmitted() )
        {
            if( !($form->isValid()) ) {
                $this->_messages->markFormInvalid();
            } else {
                $this->_manager->persist($account);
                $this->_manager->flush();

                $this->_messages->markCreateSuccess();

                if( $form->has('create_and_return') && $form->get('create_and_return')->isClicked() ) {
                    return $this->redirectToRoute('account_read');
                } else {
                    return $this->redirectToRoute('account_update', [
                        'id' => $account->getId()
                    ]);
                }
            }
        }

        $this->_breadcrumbs->add('account_read')->add('account_create');

        return $this->render('AppBundle:Entity/Account/CRUD:createItem.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/account/update/{id}",
     *      name="account_update",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function updateAction(Request $request, $id)
    {
        $account = $this->_manager->getRepository('AppBundle:Account\Account')->find($id);

        if( !$account )
            throw $this->createNotFoundException("Account identified by `id` {$id} not found");

        if( !$this->isGranted(AccountVoter::ACCOUNT_UPDATE, $account) ) {
            return $this->redirectToRoute('account_read', [
                'id' => $account->getId()
            ]);
        }

        $accountType = new AccountType(
            $this->_translator,
            $this->_accountBoundlessAccess->isGranted(AccountBoundlessAccess::ACCOUNT_CREATE)
        );

        $form = $this->createForm($accountType, $account, [
            'action' => $this->generateUrl('account_update', ['id' => $id])
        ]);

        $form->handleRequest($request);

        if( $form->isSubmitted() )
        {
            if( !($form->isValid()) ) {
                $this->_messages->markFormInvalid();
            } else {
                $this->_manager->flush();

                $this->_messages->markUpdateSuccess();

                if( $form->has('update_and_return') && $form->get('update_and_return')->isClicked() ) {
                    return $this->redirectToRoute('account_read');
                } else {
                    return $this->redirectToRoute('account_update', [
                        'id' => $account->getId()
                    ]);
                }
            }
        }

        $this->_breadcrumbs->add('account_read')->add('account_update', ['id' => $id]);

        return $this->render('AppBundle:Entity/Account/CRUD:updateItem.html.twig', [
            'form'    => $form->createView(),
            'account' => $account
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/account/delete/{id}",
     *      name="account_delete",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function deleteAction(Request $request, $id)
    {
        $account = $this->_manager->getRepository('AppBundle:Account\Account')->find($id);

        if( !$account )
            throw $this->createNotFoundException("Account identified by `id` {$id} not found");

        if( !$this->isGranted(AccountVoter::ACCOUNT_DELETE, $account) )
            throw $this->createAccessDeniedException('Access denied');

        if( !$account->getPseudoDeleted() )
        {
            $account->setPseudoDeleted(TRUE);

            $this->_manager->flush();

            $this->_messages->markDeleteSuccess();
        } else {
            $account->setPseudoDeleted(FALSE);

            $this->_manager->flush();

            $this->_messages->markUnDeleteSuccess();
        }

        return new RedirectResponse($request->headers->get('referer'));
    }
}
