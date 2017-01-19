<?php
// src/AppBundle/Controller/CRUD/TransactionController.php
namespace AppBundle\Controller\CRUD;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\RedirectResponse;

use Doctrine\ORM\EntityRepository;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Service\Common\Utility\Exceptions\SearchException,
    AppBundle\Service\Common\Utility\Exceptions\PaginatorException;

use AppBundle\Controller\Utility\Traits\EntityFilter,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Entity\Transaction\Transaction,
    AppBundle\Security\Authorization\Voter\TransactionVoter,
    AppBundle\Service\Security\TransactionBoundlessAccess;

class TransactionController extends Controller implements UserRoleListInterface
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

    /** @DI\Inject("app.security.transaction_boundless_access") */
    private $_transactionBoundlessAccess;

    /**
     * @Method({"GET"})
     * @Route(
     *      "/transaction/{id}",
     *      name="transaction_read",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "id" = null},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function readTransactionAction(Request $request, $id = NULL)
    {
        $repository = $this->_manager->getRepository('AppBundle:Transaction\Transaction');
        $routes     = [
            'read' => 'transaction_read',
            'view' => 'transaction_view',
        ];
        $templates  = [
            'readList' => 'readListTransaction',
            'readItem' => 'readItemTransaction',
        ];

        return $this->forward('AppBundle\Controller\CRUD\TransactionController::readAction', [
            'repository' => $repository,
            'routes'     => $routes,
            'templates'  => $templates,
            'id'         => $id,
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/replenishment/{id}",
     *      name="replenishment_read",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "id" = null},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function readReplenishmentAction(Request $request, $id = NULL)
    {
        $repository = $this->_manager->getRepository('AppBundle:Transaction\Replenishment');
        $routes     = [
            'read' => 'replenishment_read',
            'view' => 'replenishment_view',
        ];
        $templates  = [
            'readList' => 'readListReplenishment',
            'readItem' => 'readItemReplenishment',
        ];

        return $this->forward('AppBundle\Controller\CRUD\TransactionController::readAction', [
            'repository' => $repository,
            'routes'     => $routes,
            'templates'  => $templates,
            'id'         => $id,
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/collection/{id}",
     *      name="collection_read",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "id" = null},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function readCollectionAction(Request $request, $id = NULL)
    {
        $repository = $this->_manager->getRepository('AppBundle:Transaction\Collection');
        $routes     = [
            'read' => 'collection_read',
            'view' => 'collection_view',
        ];
        $templates  = [
            'readList' => 'readListCollection',
            'readItem' => 'readItemCollection',
        ];

        return $this->forward('AppBundle\Controller\CRUD\TransactionController::readAction', [
            'repository' => $repository,
            'routes'     => $routes,
            'templates'  => $templates,
            'id'         => $id,
        ]);
    }

    public function readAction(EntityRepository $repository, array $routes, array $templates, $id = NULL)
    {
        if( $id )
        {
            $transaction = $repository->find($id);

            if( !$transaction )
                throw $this->createNotFoundException("Transaction identified by `id` {$id} not found");

            if( !$this->isGranted(TransactionVoter::TRANSACTION_READ, $transaction) )
                throw $this->createAccessDeniedException('Access denied');

            $response = [
                'view' => sprintf('AppBundle:Entity/Transaction/CRUD/ReadItem:%s.html.twig', $templates['readItem']),
                'data' => ['transaction' => $transaction],
            ];

            $this->_breadcrumbs
                ->add($routes['read'])
                ->add($routes['read'], ['id' => $id], $this->_translator->trans($routes['view'], [], 'routes'))
            ;
        } else {
            if( !$this->_transactionBoundlessAccess->isGranted(TransactionBoundlessAccess::TRANSACTION_READ) )
                throw $this->createAccessDeniedException('Access denied');

            try {
                $this->_entityResultsManager
                    ->setPageArgument($this->_paginator->getPageArgument())
                    ->setSearchArgument($this->_search->getSearchArgument())
                ;
            } catch(PaginatorException $ex) {
                throw $this->createNotFoundException('Invalid page argument');
            } catch(SearchException $ex) {
                return $this->redirectToRoute($routes['read']);
            }

            $transactions = $this->_entityResultsManager->findRecords($repository);

            if( $transactions === FALSE )
                return $this->redirectToRoute($routes['read']);

            $transactions = $this->filterUnlessGranted(
                TransactionVoter::TRANSACTION_READ, $transactions
            );

            $response = [
                'view' => sprintf('AppBundle:Entity/Transaction/CRUD/ReadList:%s.html.twig', $templates['readList']),
                'data' => ['transactions' => $transactions],
            ];

            $this->_breadcrumbs->add($routes['read']);
        }

        return $this->render($response['view'], $response['data']);
    }
}
