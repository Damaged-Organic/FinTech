<?php
// src/AppBundle/Controller/CRUD/OperatorController.php
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
    AppBundle\Entity\Operator\Operator,
    AppBundle\Form\Type\OperatorType,
    AppBundle\Security\Authorization\Voter\OperatorVoter,
    AppBundle\Service\Security\OperatorBoundlessAccess;

class OperatorController extends Controller implements UserRoleListInterface
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

    /** @DI\Inject("app.security.operator_boundless_access") */
    private $_operatorBoundlessAccess;

    /**
     * @Method({"GET"})
     * @Route(
     *      "/operator/{id}",
     *      name="operator_read",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "id" = null},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function readAction($id = NULL)
    {
        $repository = $this->_manager->getRepository('AppBundle:Operator\Operator');

        if( $id )
        {
            $operator = $repository->find($id);

            if( !$operator )
                throw $this->createNotFoundException("Operator identified by `id` {$id} not found");

            if( !$this->isGranted(OperatorVoter::OPERATOR_READ, $operator) )
                throw $this->createAccessDeniedException('Access denied');

            $response = [
                'view' => 'AppBundle:Entity/Operator/CRUD:readItem.html.twig',
                'data' => ['operator' => $operator]
            ];

            $this->_breadcrumbs
                ->add('operator_read')
                ->add('operator_read', ['id' => $id], $this->_translator->trans('operator_view', [], 'routes'))
            ;
        } else {
            if( !$this->_operatorBoundlessAccess->isGranted(OperatorBoundlessAccess::OPERATOR_READ) )
                throw $this->createAccessDeniedException('Access denied');

            try {
                $this->_entityResultsManager
                    ->setPageArgument($this->_paginator->getPageArgument())
                    ->setSearchArgument($this->_search->getSearchArgument())
                ;
            } catch(PaginatorException $ex) {
                throw $this->createNotFoundException('Invalid page argument');
            } catch(SearchException $ex) {
                return $this->redirectToRoute('operator_read');
            }

            $operators = $this->_entityResultsManager->findRecords($repository);

            if( $operators === FALSE )
                return $this->redirectToRoute('operator_read');

            $operators = $this->filterUnlessGranted(
                OperatorVoter::OPERATOR_READ, $operators
            );

            $response = [
                'view' => 'AppBundle:Entity/Operator/CRUD:readList.html.twig',
                'data' => ['operators' => $operators]
            ];

            $this->_breadcrumbs->add('operator_read');
        }

        return $this->render($response['view'], $response['data']);
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/operator/create",
     *      name="operator_create",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"}
     * )
     */
    public function createAction(Request $request)
    {
        if( !$this->_operatorBoundlessAccess->isGranted(OperatorBoundlessAccess::OPERATOR_CREATE) )
            throw $this->createAccessDeniedException('Access denied');

        $form = $this->createForm(OperatorType::class, $operator = new Operator, [
            'action'          => $this->generateUrl('operator_create'),
            'boundlessAccess' => $this->_operatorBoundlessAccess->isGranted(OperatorBoundlessAccess::OPERATOR_CREATE),
        ]);

        $form->handleRequest($request);

        if( $form->isSubmitted() )
        {
            if( !($form->isValid()) ) {
                $this->_messages->markFormInvalid();
            } else {
                /**
                 * !!! IMPORTANT !!!
                 * This is a hack in order to get a proxy object from disabled entity field
                 * (that was explicitly typed as TextField) and set it as a real object for
                 * further persistence (in other case it will be discarded without saving)
                 */
                if( $form->has('organization') && $form->get('organization')->getData() )
                    $operator->setOrganization($form->get('organization')->getData());

                $this->_manager->persist($operator);
                $this->_manager->flush();

                $this->_messages->markCreateSuccess();

                if( $form->has('create_and_return') && $form->get('create_and_return')->isClicked() ) {
                    return $this->redirectToRoute('operator_read');
                } else {
                    return $this->redirectToRoute('operator_update', [
                        'id' => $operator->getId()
                    ]);
                }
            }
        }

        $this->_breadcrumbs->add('operator_read')->add('operator_create');

        return $this->render('AppBundle:Entity/Operator/CRUD:createItem.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/operator/update/{id}",
     *      name="operator_update",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function updateAction(Request $request, $id)
    {
        $operator = $this->_manager->getRepository('AppBundle:Operator\Operator')->find($id);

        if( !$operator )
            throw $this->createNotFoundException("Operator identified by `id` {$id} not found");

        if( !$this->isGranted(OperatorVoter::OPERATOR_UPDATE, $operator) ) {
            return $this->redirectToRoute('operator_read', [
                'id' => $operator->getId()
            ]);
        }

        $form = $this->createForm(OperatorType::class, $operator, [
            'action'                   => $this->generateUrl('operator_update', ['id' => $id]),
            'boundlessAccess'          => $this->_operatorBoundlessAccess->isGranted(OperatorBoundlessAccess::OPERATOR_CREATE),
            'updateOrganizationAccess' => $this->isGranted(OperatorVoter::OPERATOR_UPDATE_ORGANIZATION, $operator),
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
                    return $this->redirectToRoute('operator_read');
                } else {
                    return $this->redirectToRoute('operator_update', [
                        'id' => $operator->getId()
                    ]);
                }
            }
        }

        $this->_breadcrumbs->add('operator_read')->add('operator_update', ['id' => $id]);

        return $this->render('AppBundle:Entity/Operator/CRUD:updateItem.html.twig', [
            'form'     => $form->createView(),
            'operator' => $operator
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/operator/delete/{id}",
     *      name="operator_delete",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function deleteAction(Request $request, $id)
    {
        $operator = $this->_manager->getRepository('AppBundle:Operator\Operator')->find($id);

        if( !$operator )
            throw $this->createNotFoundException("Operator identified by `id` {$id} not found");

        if( !$this->isGranted(OperatorVoter::OPERATOR_DELETE, $operator) )
            throw $this->createAccessDeniedException('Access denied');

        if( !$operator->getPseudoDeleted() )
        {
            $operator->setPseudoDeleted(TRUE);

            $this->_manager->flush();

            $this->_messages->markDeleteSuccess();
        } else {
            $operator->setPseudoDeleted(FALSE);

            $this->_manager->flush();

            $this->_messages->markUnDeleteSuccess();
        }

        return new RedirectResponse($request->headers->get('referer'));
    }
}
