<?php
// src/AppBundle/Controller/CRUD/BankingMachineController.php
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
    AppBundle\Entity\BankingMachine\BankingMachine,
    AppBundle\Form\Type\BankingMachineType,
    AppBundle\Security\Authorization\Voter\BankingMachineVoter,
    AppBundle\Service\Security\BankingMachineBoundlessAccess;

class BankingMachineController extends Controller implements UserRoleListInterface
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

    /** @DI\Inject("app.security.banking_machine_boundless_access") */
    private $_bankingMachineBoundlessAccess;

    /** @DI\Inject("utility.security.password_encoder") */
    private $_passwordEncoder;

    /**
     * @Method({"GET"})
     * @Route(
     *      "/banking_machine/{id}",
     *      name="banking_machine_read",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "id" = null},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function readAction($id = NULL)
    {
        $repository = $this->_manager->getRepository('AppBundle:BankingMachine\BankingMachine');

        if( $id )
        {
            $bankingMachine = $repository->find($id);

            if( !$bankingMachine )
                throw $this->createNotFoundException("Banking Machine identified by `id` {$id} not found");

            if( !$this->isGranted(BankingMachineVoter::BANKING_MACHINE_READ, $bankingMachine) )
                throw $this->createAccessDeniedException('Access denied');

            $response = [
                'view' => 'AppBundle:Entity/BankingMachine/CRUD:readItem.html.twig',
                'data' => ['bankingMachine' => $bankingMachine]
            ];

            $this->_breadcrumbs
                ->add('banking_machine_read')
                ->add('banking_machine_read', ['id' => $id], $this->_translator->trans('banking_machine_view', [], 'routes'))
            ;
        } else {
            if( !$this->_bankingMachineBoundlessAccess->isGranted(BankingMachineBoundlessAccess::BANKING_MACHINE_READ) )
                throw $this->createAccessDeniedException('Access denied');

            try {
                $this->_entityResultsManager
                    ->setPageArgument($this->_paginator->getPageArgument())
                    ->setSearchArgument($this->_search->getSearchArgument())
                ;
            } catch(PaginatorException $ex) {
                throw $this->createNotFoundException('Invalid page argument');
            } catch(SearchException $ex) {
                return $this->redirectToRoute('banking_machine_read');
            }

            $bankingMachines = $this->_entityResultsManager->findRecords($repository);

            if( $bankingMachines === FALSE )
                return $this->redirectToRoute('banking_machine_read');

            $bankingMachines = $this->filterUnlessGranted(
                BankingMachineVoter::BANKING_MACHINE_READ, $bankingMachines
            );

            $response = [
                'view' => 'AppBundle:Entity/BankingMachine/CRUD:readList.html.twig',
                'data' => ['bankingMachines' => $bankingMachines]
            ];

            $this->_breadcrumbs->add('banking_machine_read');
        }

        return $this->render($response['view'], $response['data']);
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/banking_machine/create",
     *      name="banking_machine_create",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"}
     * )
     */
    public function createAction(Request $request)
    {
        if( !$this->_bankingMachineBoundlessAccess->isGranted(BankingMachineBoundlessAccess::BANKING_MACHINE_CREATE) )
            throw $this->createAccessDeniedException('Access denied');

        $form = $this->createForm(BankingMachineType::class, $bankingMachine = new BankingMachine, [
            'action'          => $this->generateUrl('banking_machine_create'),
            'boundlessAccess' => $this->_bankingMachineBoundlessAccess->isGranted(BankingMachineBoundlessAccess::BANKING_MACHINE_CREATE),
        ]);

        $form->handleRequest($request);

        if( $form->isSubmitted() )
        {
            if( !($form->isValid()) ) {
                $this->_messages->markFormInvalid();
            } else {
                $encodedPassword = $this->_passwordEncoder
                    ->encodePassword($bankingMachine->getPassword())
                ;

                $bankingMachine->setPassword($encodedPassword);

                $this->_manager->persist($bankingMachine);
                $this->_manager->flush();

                $this->_messages->markCreateSuccess();

                if( $form->has('create_and_return') && $form->get('create_and_return')->isClicked() ) {
                    return $this->redirectToRoute('banking_machine_read');
                } else {
                    return $this->redirectToRoute('banking_machine_update', [
                        'id' => $region->getId()
                    ]);
                }
            }
        }

        $this->_breadcrumbs->add('banking_machine_read')->add('banking_machine_create');

        return $this->render('AppBundle:Entity/BankingMachine/CRUD:createItem.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/banking_machine/update/{id}",
     *      name="banking_machine_update",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function updateAction(Request $request, $id)
    {
        $bankingMachine = $this->_manager->getRepository('AppBundle:BankingMachine\BankingMachine')->find($id);

        if( !$bankingMachine )
            throw $this->createNotFoundException("Banking Machine identified by `id` {$id} not found");

        if( !$this->isGranted(BankingMachineVoter::BANKING_MACHINE_UPDATE, $bankingMachine) ) {
            return $this->redirectToRoute('banking_machine_read', [
                'id' => $bankingMachine->getId()
            ]);
        }

        $form = $this->createForm(BankingMachineType::class, $bankingMachine, [
            'action'          => $this->generateUrl('banking_machine_update', ['id' => $id]),
            'boundlessAccess' => $this->_bankingMachineBoundlessAccess->isGranted(BankingMachineBoundlessAccess::BANKING_MACHINE_CREATE),
        ]);

        $form->handleRequest($request);

        if( $form->isSubmitted() )
        {
            if( !($form->isValid()) ) {
                $this->_messages->markFormInvalid();
            } else {
                if( $form->has('password') && $form->get('password')->getData() )
                {
                    $encodedPassword = $this->_passwordEncoder
                        ->encodePassword($bankingMachine->getPassword())
                    ;

                    $bankingMachine->setPassword($encodedPassword);
                }

                $this->_manager->flush();

                $this->_messages->markUpdateSuccess();

                if( $form->has('update_and_return') && $form->get('update_and_return')->isClicked() ) {
                    return $this->redirectToRoute('banking_machine_read');
                } else {
                    return $this->redirectToRoute('banking_machine_update', [
                        'id' => $bankingMachine->getId()
                    ]);
                }
            }
        }

        $this->_breadcrumbs->add('banking_machine_read')->add('banking_machine_update', ['id' => $id]);

        return $this->render('AppBundle:Entity/BankingMachine/CRUD:updateItem.html.twig', [
            'form'           => $form->createView(),
            'bankingMachine' => $bankingMachine
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/banking_machine/delete/{id}",
     *      name="banking_machine_delete",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale_dashboard%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function deleteAction(Request $request, $id)
    {
        $bankingMachine = $this->_manager->getRepository('AppBundle:BankingMachine\BankingMachine')->find($id);

        if( !$bankingMachine )
            throw $this->createNotFoundException("Banking Machine identified by `id` {$id} not found");

        if( !$this->isGranted(BankingMachineVoter::BANKING_MACHINE_DELETE, $bankingMachine) )
            throw $this->createAccessDeniedException('Access denied');

        if( !$bankingMachine->getPseudoDeleted() )
        {
            $bankingMachine->setPseudoDeleted(TRUE);

            $this->_manager->flush();

            $this->_messages->markDeleteSuccess();
        } else {
            $bankingMachine->setPseudoDeleted(FALSE);

            $this->_manager->flush();

            $this->_messages->markUnDeleteSuccess();
        }

        return $this->redirectToRoute('banking_machine_read');
    }
}
