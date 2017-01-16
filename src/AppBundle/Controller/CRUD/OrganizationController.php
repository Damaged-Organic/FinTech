<?php
// src/AppBundle/Controller/CRUD/OrganizationController.php
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
    AppBundle\Entity\Organization\Organization,
    AppBundle\Form\Type\OrganizationType,
    AppBundle\Security\Authorization\Voter\OrganizationVoter,
    AppBundle\Service\Security\OrganizationBoundlessAccess;

class OrganizationController extends Controller implements UserRoleListInterface
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

    /** @DI\Inject("app.security.organization_boundless_access") */
    private $_organizationBoundlessAccess;

    /**
     * @Method({"GET"})
     * @Route(
     *      "/organization/{id}",
     *      name="organization_read",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "id" = null},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function readAction($id = NULL)
    {
        $repository = $this->_manager->getRepository('AppBundle:Organization\Organization');

        if( $id )
        {
            $organization = $repository->find($id);

            if( !$organization )
                throw $this->createNotFoundException("Organization identified by `id` {$id} not found");

            if( !$this->isGranted(OrganizationVoter::ORGANIZATION_READ, $organization) )
                throw $this->createAccessDeniedException('Access denied');

            $response = [
                'view' => 'AppBundle:Entity/Organization/CRUD:readItem.html.twig',
                'data' => ['organization' => $organization]
            ];

            $this->_breadcrumbs
                ->add('organization_read')
                ->add('organization_read', ['id' => $id], $this->_translator->trans('organization_view', [], 'routes'))
            ;
        } else {
            if( !$this->_organizationBoundlessAccess->isGranted(OrganizationBoundlessAccess::ORGANIZATION_READ) )
                throw $this->createAccessDeniedException('Access denied');

            try {
                $this->_entityResultsManager
                    ->setPageArgument($this->_paginator->getPageArgument())
                    ->setSearchArgument($this->_search->getSearchArgument())
                ;
            } catch(PaginatorException $ex) {
                throw $this->createNotFoundException('Invalid page argument');
            } catch(SearchException $ex) {
                return $this->redirectToRoute('organization_read');
            }

            $organizations = $this->_entityResultsManager->findRecords($repository);

            if( $organizations === FALSE )
                return $this->redirectToRoute('organization_read');

            $organizations = $this->filterUnlessGranted(
                OrganizationVoter::ORGANIZATION_READ, $organizations
            );

            $response = [
                'view' => 'AppBundle:Entity/Organization/CRUD:readList.html.twig',
                'data' => ['organizations' => $organizations]
            ];

            $this->_breadcrumbs->add('organization_read');
        }

        return $this->render($response['view'], $response['data']);
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/organization/create",
     *      name="organization_create",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"}
     * )
     */
    public function createAction(Request $request)
    {
        if( !$this->_organizationBoundlessAccess->isGranted(OrganizationBoundlessAccess::ORGANIZATION_CREATE) )
            throw $this->createAccessDeniedException('Access denied');

        $form = $this->createForm(OrganizationType::class, $organization = new Organization, [
            'action'          => $this->generateUrl('organization_create'),
            'boundlessAccess' => $this->_organizationBoundlessAccess->isGranted(OrganizationBoundlessAccess::ORGANIZATION_CREATE)
        ]);

        $form->handleRequest($request);

        if( $form->isSubmitted() )
        {
            if( !($form->isValid()) ) {
                $this->_messages->markFormInvalid();
            } else {
                $this->_manager->persist($organization);
                $this->_manager->flush();

                $this->_messages->markCreateSuccess();

                if( $form->has('create_and_return') && $form->get('create_and_return')->isClicked() ) {
                    return $this->redirectToRoute('organization_read');
                } else {
                    return $this->redirectToRoute('organization_update', [
                        'id' => $organization->getId()
                    ]);
                }
            }
        }

        $this->_breadcrumbs->add('organization_read')->add('organization_create');

        return $this->render('AppBundle:Entity/Organization/CRUD:createItem.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/organization/update/{id}",
     *      name="organization_update",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function updateAction(Request $request, $id)
    {
        $organization = $this->_manager->getRepository('AppBundle:Organization\Organization')->find($id);

        if( !$organization )
            throw $this->createNotFoundException("Organization identified by `id` {$id} not found");

        if( !$this->isGranted(OrganizationVoter::ORGANIZATION_UPDATE, $organization) ) {
            return $this->redirectToRoute('organization_read', [
                'id' => $organization->getId()
            ]);
        }

        $form = $this->createForm(OrganizationType::class, $organization, [
            'action'          => $this->generateUrl('organization_update', ['id' => $id]),
            'boundlessAccess' => $this->_organizationBoundlessAccess->isGranted(OrganizationBoundlessAccess::ORGANIZATION_CREATE),
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
                    return $this->redirectToRoute('organization_read');
                } else {
                    return $this->redirectToRoute('organization_update', [
                        'id' => $organization->getId()
                    ]);
                }
            }
        }

        $this->_breadcrumbs->add('organization_read')->add('organization_update', ['id' => $id]);

        return $this->render('AppBundle:Entity/Organization/CRUD:updateItem.html.twig', [
            'form'         => $form->createView(),
            'organization' => $organization
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/organization/delete/{id}",
     *      name="organization_delete",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function deleteAction(Request $request, $id)
    {
        $organization = $this->_manager->getRepository('AppBundle:Organization\Organization')->find($id);

        if( !$organization )
            throw $this->createNotFoundException("Organization identified by `id` {$id} not found");

        if( !$this->isGranted(OrganizationVoter::ORGANIZATION_DELETE, $organization) )
            throw $this->createAccessDeniedException('Access denied');

        if( !$organization->getPseudoDeleted() )
        {
            $organization->setPseudoDeleted(TRUE);

            $this->_manager->flush();

            $this->_messages->markDeleteSuccess();
        } else {
            $organization->setPseudoDeleted(FALSE);

            $this->_manager->flush();

            $this->_messages->markUnDeleteSuccess();
        }

        return new RedirectResponse($request->headers->get('referer'));
    }
}
