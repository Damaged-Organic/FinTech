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
}
