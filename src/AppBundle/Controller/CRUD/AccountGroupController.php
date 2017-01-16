<?php
// src/AppBundle/Controller/CRUD/AccountGroupController.php
namespace AppBundle\Controller\CRUD;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\RedirectResponse;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Service\Common\Utility\Exceptions\SearchException,
    AppBundle\Service\Common\Utility\Exceptions\PaginatorException;

use AppBundle\Controller\Utility\Traits\EntityFilter,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Entity\Account\AccountGroup,
    AppBundle\Form\Type\AccountGroupType,
    AppBundle\Security\Authorization\Voter\AccountGroupVoter,
    AppBundle\Service\Security\AccountGroupBoundlessAccess;

class AccountGroupController extends Controller implements UserRoleListInterface
{
    use EntityFilter;

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

    /**
     * @Method({"GET"})
     * @Route(
     *      "/account_group/{id}",
     *      name="account_group_read",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "id" = null},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function readAction($id = NULL)
    {
        $repository = $this->_manager->getRepository('AppBundle:Account\AccountGroup');

        if( $id )
        {
            $accountGroup = $repository->find($id);

            if( !$accountGroup )
                throw $this->createNotFoundException("Account Group identified by `id` {$id} not found");

            if( !$this->isGranted(AccountGroupVoter::ACCOUNT_GROUP_READ, $accountGroup) )
                throw $this->createAccessDeniedException('Access denied');

            $response = [
                'view' => 'AppBundle:Entity/AccountGroup/CRUD:readItem.html.twig',
                'data' => ['accountGroup' => $accountGroup]
            ];

            $this->_breadcrumbs
                ->add('account_group_read')
                ->add('account_group_read', ['id' => $id], $this->_translator->trans('account_group_view', [], 'routes'))
            ;
        } else {
            if( !$this->_accountGroupBoundlessAccess->isGranted(AccountGroupBoundlessAccess::ACCOUNT_GROUP_READ) )
                throw $this->createAccessDeniedException('Access denied');

            try {
                $this->_entityResultsManager
                    ->setPageArgument($this->_paginator->getPageArgument())
                    ->setSearchArgument($this->_search->getSearchArgument())
                ;
            } catch(PaginatorException $ex) {
                throw $this->createNotFoundException('Invalid page argument');
            } catch(SearchException $ex) {
                return $this->redirectToRoute('account_group_read');
            }

            $accountGroups = $this->_entityResultsManager->findRecords($repository);

            if( $accountGroups === FALSE )
                return $this->redirectToRoute('account_group_read');

            $accountGroups = $this->filterUnlessGranted(
                AccountGroupVoter::ACCOUNT_GROUP_READ, $accountGroups
            );

            $response = [
                'view' => 'AppBundle:Entity/AccountGroup/CRUD:readList.html.twig',
                'data' => ['accountGroups' => $accountGroups]
            ];

            $this->_breadcrumbs->add('account_group_read');
        }

        return $this->render($response['view'], $response['data']);
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/account_group/create",
     *      name="account_group_create",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"}
     * )
     */
    public function createAction(Request $request)
    {
        if( !$this->_accountGroupBoundlessAccess->isGranted(AccountGroupBoundlessAccess::ACCOUNT_GROUP_CREATE) )
            throw $this->createAccessDeniedException('Access denied');

        $form = $this->createForm(AccountGroupType::class, $accountGroup = new AccountGroup, [
            'action'              => $this->generateUrl('account_group_create'),
            'boundlessReadAccess' => $this->_accountGroupBoundlessAccess->isGranted(AccountGroupBoundlessAccess::ACCOUNT_GROUP_READ),
        ]);

        $form->handleRequest($request);

        if( $form->isSubmitted() )
        {
            if( !($form->isValid()) ) {
                $this->_messages->markFormInvalid();
            } else {
                $this->_manager->persist($accountGroup);
                $this->_manager->flush();

                $this->_messages->markCreateSuccess();

                if( $form->has('create_and_return') && $form->get('create_and_return')->isClicked() ) {
                    return $this->redirectToRoute('account_group_read');
                } else {
                    return $this->redirectToRoute('account_group_update', [
                        'id' => $accountGroup->getId()
                    ]);
                }
            }
        }

        $this->_breadcrumbs->add('account_group_read')->add('account_group_create');

        return $this->render('AppBundle:Entity/AccountGroup/CRUD:createItem.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/account_group/update/{id}",
     *      name="account_group_update",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function updateAction(Request $request, $id)
    {
        $accountGroup = $this->_manager->getRepository('AppBundle:Account\AccountGroup')->find($id);

        if( !$accountGroup )
            throw $this->createNotFoundException("Account Group identified by `id` {$id} not found");

        if( !$this->isGranted(AccountGroupVoter::ACCOUNT_GROUP_UPDATE, $accountGroup) ) {
            return $this->redirectToRoute('account_group_read', [
                'id' => $accountGroup->getId()
            ]);
        }

        $form = $this->createForm(AccountGroupType::class, $accountGroup, [
            'action'                   => $this->generateUrl('account_group_update', ['id' => $id]),
            'boundlessReadAccess'      => $this->_accountGroupBoundlessAccess->isGranted(AccountGroupBoundlessAccess::ACCOUNT_GROUP_READ),
            'updateOrganizationAccess' => $this->isGranted(AccountGroupVoter::ACCOUNT_GROUP_UPDATE_ORGANIZATION, $accountGroup),
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
                    return $this->redirectToRoute('account_group_read');
                } else {
                    return $this->redirectToRoute('account_group_update', [
                        'id' => $accountGroup->getId()
                    ]);
                }
            }
        }

        $this->_breadcrumbs->add('account_group_read')->add('account_group_update', ['id' => $id]);

        return $this->render('AppBundle:Entity/AccountGroup/CRUD:updateItem.html.twig', [
            'form'         => $form->createView(),
            'accountGroup' => $accountGroup
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/account_group/delete/{id}",
     *      name="account_group_delete",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function deleteAction(Request $request, $id)
    {
        $accountGroup = $this->_manager->getRepository('AppBundle:Account\AccountGroup')->find($id);

        if( !$accountGroup )
            throw $this->createNotFoundException("Account Group identified by `id` {$id} not found");

        if( !$this->isGranted(AccountGroupVoter::ACCOUNT_GROUP_DELETE, $accountGroup) )
            throw $this->createAccessDeniedException('Access denied');

        if( !$accountGroup->getPseudoDeleted() )
        {
            $accountGroup->setPseudoDeleted(TRUE);

            $this->_manager->flush();

            $this->_messages->markDeleteSuccess();
        } else {
            $accountGroup->setPseudoDeleted(FALSE);

            $this->_manager->flush();

            $this->_messages->markUnDeleteSuccess();
        }

        return new RedirectResponse($request->headers->get('referer'));
    }
}
