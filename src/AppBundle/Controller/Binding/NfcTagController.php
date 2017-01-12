<?php
// src/AppBundle/Controller/Binding/NfcTagController.php
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

use AppBundle\Entity\NfcTag\NfcTag,
    AppBundle\Security\Authorization\Voter\NfcTagVoter,
    AppBundle\Service\Security\NfcTagBoundlessAccess;

use AppBundle\Entity\Operator\Operator,
    AppBundle\Security\Authorization\Voter\OperatorVoter;

use AppBundle\Entity\Operator\Collector,
    AppBundle\Entity\Operator\Cashier;

class NfcTagController extends Controller implements UserRoleListInterface
{
    use ClassOperationsTrait, EntityFilter;

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

    /** @DI\Inject("app.security.nfc_tag_boundless_access") */
    private $_nfcTagBoundlessAccess;

    public function showAction($objectClass, $objectId)
    {
        if( !$this->_nfcTagBoundlessAccess->isGranted(NfcTagBoundlessAccess::NFC_TAG_READ) )
            throw $this->createAccessDeniedException('Access denied');

        switch(TRUE)
        {
            case $this->compareObjectToStringInstance(new Operator, $objectClass, 'AppBundle\Entity\Operator'):
                $object = $this->_manager->getRepository('AppBundle:Operator\Operator')->find($objectId);

                if( !$object )
                    throw $this->createNotFoundException("Operator identified by `id` {$objectId} not found");

                /*
                 * TRICKY: single nfcTag object pushed into
                 * array in order to be valid for template
                 */
                $nfcTags = $this->filterDeletedIfNotGranted(
                    NfcTagVoter::NFC_TAG_READ,
                    (( $object->getNfcTag() ) ? [$object->getNfcTag()] : NULL)
                );

                $action = [
                    'path'  => 'nfc_tag_choose',
                    'voter' => OperatorVoter::OPERATOR_BIND
                ];
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported 1");
            break;
        }

        return $this->render('AppBundle:Entity/NfcTag/Binding:show.html.twig', [
            'standalone' => TRUE,
            'nfcTags'    => $nfcTags,
            'object'     => $object,
            'action'     => $action
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/nfc_tag/choose_for/{objectClass}/{objectId}",
     *      name="nfc_tag_choose",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function chooseAction($objectClass, $objectId)
    {
        if( !$this->_nfcTagBoundlessAccess->isGranted(NfcTagBoundlessAccess::NFC_TAG_BIND) )
            throw $this->createAccessDeniedException('Access denied');

        switch(TRUE)
        {
            case $this->compareObjectToStringInstance(new Operator, $objectClass, 'AppBundle\Entity\Operator'):
                $operator = $object = $this->_manager->getRepository('AppBundle:Operator\Operator')->find($objectId);

                if( !$operator )
                    throw $this->createNotFoundException("Operator identified by `id` {$objectId} not found");

                $path = 'operator_update_bounded';

                $this->_breadcrumbs
                    ->add('operator_read')
                    ->add('operator_update', ['id' => $objectId])
                    ->add('operator_update_bounded', [
                        'objectId'    => $objectId,
                        'objectClass' => 'nfctag'
                    ], $this->_translator->trans('nfc_tag_read', [], 'routes'))
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
            return $this->redirectToRoute('nfc_tag_choose', $routeArguments);
        }

        $nfcTags = $this->_entityResultsManager->findRecords(
            $this->_manager->getRepository('AppBundle:NfcTag\NfcTag')
        );

        if( $nfcTags === FALSE )
            return $this->redirectToRoute('nfc_tag_choose', $routeArguments);

        $nfcTags = $this->filterDeletedIfNotGranted(
            NfcTagVoter::NFC_TAG_READ, $nfcTags
        );

        $this->_breadcrumbs->add('nfc_tag_choose', $routeArguments);

        return $this->render('AppBundle:Entity/NfcTag/Binding:choose.html.twig', [
            'path'    => $path,
            'nfcTags' => $nfcTags,
            'object'  => $object
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/nfc_tag/bind/{targetId}/{objectClass}/{objectId}",
     *      name="nfc_tag_bind",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "targetId" = "\d+", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function bindToAction(Request $request, $targetId, $objectClass, $objectId)
    {
        $nfcTag = $this->_manager->getRepository('AppBundle:NfcTag\NfcTag')->find($targetId);

        if( !$nfcTag )
            throw $this->createNotFoundException($this->_translator->trans('common.error.not_found', [], 'responses'));

        if( !$this->isGranted(NfcTagVoter::NFC_TAG_BIND, $nfcTag) )
            throw $this->createAccessDeniedException($this->_translator->trans('common.error.forbidden', [], 'responses'));

        switch(TRUE)
        {
            case $this->compareObjectToStringInstance(new Operator, $objectClass, 'AppBundle\Entity\Operator'):
                $operator = $this->_manager->getRepository('AppBundle:Operator\Operator')->find($objectId);

                if( !$operator )
                    throw $this->createNotFoundException($this->_translator->trans('common.error.not_found', [], 'responses'));

                /*
                 * TRICKY: had to set NfcTag as the owning side of the oneToOne
                 * relationship due to possible change to manyToOne. If so, an
                 * `operator_id` column will remain unchanged.
                 * But in that case simply persisting nfcTag is not enough, as
                 * it could generate an integrity violation exception; so
                 * operator's nfcTag relationship should first be persisted as
                 * `NULL` and flushed in order to break that relationship.
                 */
                $this->_manager->transactional(function($_manager) use($operator, $nfcTag)
                {
                    if( $operator->getNfcTag() )
                    {
                        $_manager->persist(
                            $operator->getNfcTag()->setOperator(NULL)
                        );
                        $_manager->flush();
                    }

                    $nfcTag->setOperator($operator);

                    $nfcTag->deactivate();

                    $_manager->persist($nfcTag);
                });
            break;

            default:
                throw new NotAcceptableHttpException($this->_translator->trans('bind.error.not_boundalbe', [], 'responses'));
            break;
        }

        $this->_manager->flush();

        $this->_messages->markBindSuccess(
            $this->_translator->trans('bind.success.nfc_tag', [], 'responses')
        );

        return new RedirectResponse($request->headers->get('referer'));
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/nfc_tag/unbind/{targetId}/{objectClass}/{objectId}",
     *      name="nfc_tag_unbind",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "targetId" = "\d+", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function unbindFromAction(Request $request, $targetId, $objectClass, $objectId)
    {
        $nfcTag = $this->_manager->getRepository('AppBundle:NfcTag\NfcTag')->find($targetId);

        if( !$nfcTag )
            throw $this->createNotFoundException($this->_translator->trans('common.error.not_found', [], 'responses'));

        if( !$this->isGranted(NfcTagVoter::NFC_TAG_BIND, $nfcTag) )
            throw $this->createAccessDeniedException($this->_translator->trans('common.error.forbidden', [], 'responses'));

        switch(TRUE)
        {
            case $this->compareObjectToStringInstance(new Operator, $objectClass, 'AppBundle\Entity\Operator'):
                $nfcTag->deactivate();

                $nfcTag->setOperator(NULL);
            break;

            default:
                throw new NotAcceptableHttpException($this->_translator->trans('bind.error.not_unboundable', [], 'responses'));
            break;
        }

        $this->_manager->flush();

        $this->_messages->markUnbindSuccess(
            $this->_translator->trans('unbind.success.nfc_tag', [], 'responses')
        );

        return new RedirectResponse($request->headers->get('referer'));
    }
}
