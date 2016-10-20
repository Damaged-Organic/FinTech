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

use AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Entity\Organization\Organization,
    AppBundle\Form\Type\OrganizationType,
    AppBundle\Security\Authorization\Voter\OrganizationVoter,
    AppBundle\Service\Security\OrganizationBoundlessAccess;

class OrganizationController extends Controller implements UserRoleListInterface
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

            $response = [
                'view' => 'AppBundle:Entity/Organization/CRUD:readList.html.twig',
                'data' => ['organizations' => $organizations]
            ];

            $this->_breadcrumbs->add('organization_read');
        }

        return $this->render($response['view'], $response['data']);
    }
}
