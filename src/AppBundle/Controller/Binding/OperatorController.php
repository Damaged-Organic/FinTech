<?php
// src/AppBundle/Controller/Binding/OperatorController.php
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

use AppBundle\Entity\Operator\Operator,
    AppBundle\Security\Authorization\Voter\OperatorVoter,
    AppBundle\Service\Security\OperatorBoundlessAccess;

use AppBundle\Entity\Organization\Organization,
    AppBundle\Security\Authorization\Voter\OrganizationVoter;

use AppBundle\Entity\BankingMachine\BankingMachine,
    AppBundle\Security\Authorization\Voter\BankingMachineVoter;

use AppBundle\Entity\NfcTag\NfcTag;

class OperatorController extends Controller implements UserRoleListInterface
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

    /** @DI\Inject("app.security.operator_boundless_access") */
    private $_operatorBoundlessAccess;

    public function showAction($objectClass, $objectId)
    {
        if( !$this->_operatorBoundlessAccess->isGranted(OperatorBoundlessAccess::OPERATOR_READ) )
            throw $this->createAccessDeniedException('Access denied');

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Organization, $objectClass):
                $object = $this->_manager->getRepository('AppBundle:Organization\Organization')->find($objectId);

                if( !$object )
                    throw $this->createNotFoundException("Organization identified by `id` {$objectId} not found");

                $action = [
                    'path'  => 'organization_choose',
                    'voter' => OrganizationVoter::ORGANIZATION_BIND
                ];
            break;

            case $this->compareObjectClassNameToString(new BankingMachine, $objectClass):
                $object = $this->_manager->getRepository('AppBundle:BankingMachine\BankingMachine')->find($objectId);

                if( !$object )
                    throw $this->createNotFoundException("Banking Machine identified by `id` {$objectId} not found");

                $action = [
                    'path'  => 'banking_machine_choose',
                    'voter' => BankingMachineVoter::BANKING_MACHINE_BIND
                ];
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        $route          = $this->_requestStack->getMasterRequest()->get('_route');
        $routeArguments = [
            'objectId'    => $objectId,
            'objectClass' => $this->getObjectClassNameLower(new Operator)
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

        $operators = $this->_entityResultsManager->findRecords($object->getOperators());

        if( $operators === FALSE )
            return $this->redirectToRoute($route, $routeArguments);

        $operators = $this->filterDeletedIfNotGranted(
            OperatorVoter::OPERATOR_READ, $operators
        );

        return $this->render('AppBundle:Entity/Operator/Binding:show.html.twig', [
            'standalone' => TRUE,
            'operators'  => $operators,
            'object'     => $object,
            'action'     => $action
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/operator/update/{objectId}/bounded/{objectClass}",
     *      name="operator_update_bounded",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "objectId" = "\d+", "objectClass" = "[a-z]+"}
     * )
     */
    public function boundedAction($objectId, $objectClass)
    {
        $operator = $this->_manager->getRepository('AppBundle:Operator\Operator')->find($objectId);

        if( !$operator )
            throw $this->createNotFoundException("Operator identified by `id` {$objectId} not found");

        if( !$this->isGranted(OperatorVoter::OPERATOR_READ, $operator) )
            throw $this->createAccessDeniedException('Access denied');

        $this->_breadcrumbs
            ->add('operator_read')
            ->add('operator_update', [
                'id' => $objectId
            ], $this->_translator->trans('operator_bounded', [], 'routes'))
        ;

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new NfcTag, $objectClass):
                $bounded = $this->forward('AppBundle:Binding\NfcTag:show', [
                    'objectClass' => $this->getObjectClassName($operator),
                    'objectId'    => $objectId
                ]);

                $this->_breadcrumbs
                    ->add('operator_update_bounded', [
                        'objectId'    => $objectId,
                        'objectClass' => $objectClass
                    ], $this->_translator->trans('nfc_tag_read', [], 'routes'))
                ;
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        return $this->render('AppBundle:Entity/Operator/Binding:bounded.html.twig', [
            'objectClass' => $objectClass,
            'bounded'     => $bounded->getContent(),
            'operator'    => $operator
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/operator/choose_for/{objectClass}/{objectId}",
     *      name="operator_choose",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function chooseAction($objectClass, $objectId)
    {
        if( !$this->_operatorBoundlessAccess->isGranted(OperatorBoundlessAccess::OPERATOR_BIND) )
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
                        'objectClass' => 'operator'
                    ], $this->_translator->trans('operator_read', [], 'routes'))
                ;
            break;

            case $this->compareObjectClassNameToString(new BankingMachine, $objectClass):
                $bankingMachine = $object = $this->_manager->getRepository('AppBundle:BankingMachine\BankingMachine')->find($objectId);

                if( !$bankingMachine )
                    throw $this->createNotFoundException("Banking Machine identified by `id` {$objectId} not found");

                $path = 'banking_machine_update_bounded';

                $this->_breadcrumbs
                    ->add('banking_machine_read')
                    ->add('banking_machine_update', ['id' => $objectId])
                    ->add('banking_machine_update_bounded', [
                        'objectId'    => $objectId,
                        'objectClass' => 'operator'
                    ], $this->_translator->trans('operator_read', [], 'routes'))
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
            return $this->redirectToRoute('student_choose', $routeArguments);
        }

        $operators = $this->_entityResultsManager->findRecords(
            $this->_manager->getRepository('AppBundle:Operator\Operator')
        );

        if( $operators === FALSE )
            return $this->redirectToRoute('operator_choose', $routeArguments);

        $operators = $this->filterDeletedIfNotGranted(
            OperatorVoter::OPERATOR_READ, $operators
        );

        $this->_breadcrumbs->add('operator_choose', $routeArguments);

        return $this->render('AppBundle:Entity/Operator/Binding:choose.html.twig', [
            'path'      => $path,
            'operators' => $operators,
            'object'    => $object
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/operator/bind/{targetId}/{objectClass}/{objectId}",
     *      name="operator_bind",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "targetId" = "\d+", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function bindToAction(Request $request, $targetId, $objectClass, $objectId)
    {
        $operator = $this->_manager->getRepository('AppBundle:Operator\Operator')->find($targetId);

        if( !$operator )
            throw $this->createNotFoundException($this->_translator->trans('common.error.not_found', [], 'responses'));

        if( !$this->isGranted(OperatorVoter::OPERATOR_BIND, $operator) )
            throw $this->createAccessDeniedException($this->_translator->trans('common.error.forbidden', [], 'responses'));

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Organization, $objectClass):
                $organization = $this->_manager->getRepository('AppBundle:Organization\Organization')->find($objectId);

                if( !$organization )
                    throw $this->createNotFoundException($this->_translator->trans('common.error.not_found', [], 'responses'));

                $organization->addOperator($operator);

                $this->_manager->persist($organization);
            break;

            case $this->compareObjectClassNameToString(new BankingMachine, $objectClass):
                $bankingMachine = $this->_manager->getRepository('AppBundle:BankingMachine\BankingMachine')->find($objectId);

                if( !$bankingMachine )
                    throw $this->createNotFoundException($this->_translator->trans('common.error.not_found', [], 'responses'));

                $bankingMachine->addOperator($operator);

                $this->_manager->persist($bankingMachine);
            break;

            default:
                throw new NotAcceptableHttpException($this->_translator->trans('bind.error.not_boundalbe', [], 'responses'));
            break;
        }

        $this->_manager->flush();

        $this->_messages->markBindSuccess(
            $this->_translator->trans('bind.success.operator', [], 'responses')
        );

        return new RedirectResponse($request->headers->get('referer'));
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/operator/unbind/{targetId}/{objectClass}/{objectId}",
     *      name="operator_unbind",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "targetId" = "\d+", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function unbindFromAction(Request $request, $targetId, $objectClass, $objectId)
    {
        $operator = $this->_manager->getRepository('AppBundle:Operator\Operator')->find($targetId);

        if( !$operator )
            throw $this->createNotFoundException($this->_translator->trans('common.error.not_found', [], 'responses'));

        if( !$this->isGranted(OperatorVoter::OPERATOR_BIND, $operator) )
            throw $this->createAccessDeniedException($this->_translator->trans('common.error.forbidden', [], 'responses'));

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Organization, $objectClass):
                $operator->setOrganization(NULL);
            break;

            case $this->compareObjectClassNameToString(new BankingMachine, $objectClass):
                $operator->setBankingMachine(NULL);
            break;

            default:
                throw new NotAcceptableHttpException($this->_translator->trans('bind.error.not_unboundalbe', [], 'responses'));
            break;
        }

        $this->_manager->flush();

        $this->_messages->markUnbindSuccess(
            $this->_translator->trans('unbind.success.operator', [], 'responses')
        );

        return new RedirectResponse($request->headers->get('referer'));
    }
}
