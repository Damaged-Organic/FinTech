<?php
// src/SyncBundle/Controller/BankingMachineController.php
namespace SyncBundle\Controller;

use RuntimeException;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpKernel\Exception\NotFoundHttpException,
    Symfony\Component\HttpKernel\Exception\BadRequestHttpException,
    Symfony\Component\HttpKernel\Exception\FatalErrorException,
    Symfony\Component\Security\Core\Exception\BadCredentialsException;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Controller\Utility\Traits\EntityFilter,
    AppBundle\Entity\BankingMachine\BankingMachineSync;

use SyncBundle\EventListener\Security\Markers\AuthorizationMarkerInterface;

class BankingMachineController extends Controller implements AuthorizationMarkerInterface
{
    use EntityFilter;

    /** @DI\Inject("doctrine.orm.entity_manager") */
    private $_manager;

    /** @DI\Inject("sync.banking_machine.sync.formatter") */
    private $_formatter;

    /** @DI\Inject("sync.banking_machine.sync.validator.structure") */
    private $_structureValidator;

    /** @DI\Inject("sync.banking_machine.sync.validator.sequence") */
    private $_sequenceValidator;

    /** @DI\Inject("sync.banking_machine.sync.handler") */
    private $_handler;

    /** @DI\Inject("sync.banking_machine.sync.recorder") */
    private $_recorder;

    /** @DI\Inject("app.serializer.banking_machine") */
    private $_bankingMachineSerializer;

    /** @DI\Inject("app.serializer.banking_machine_sync") */
    private $_bankingMachineSyncSerializer;

    /** @DI\Inject("app.serializer.operator") */
    private $_operatorSerializer;

    /** @DI\Inject("app.serializer.account_group") */
    private $_accountGroupSerializer;

    /** @DI\Inject("app.serializer.replenishment") */
    private $_replenishmentSerializer;

    /**
     * @Method({"GET"})
     * @Route(
     *      "/banking_machines/{serial}/syncs",
     *      name = "sync_get_banking_machines_syncs",
     *      host = "{domain_api_v_1}",
     *      schemes = {"http"},
     *      defaults = {"_locale" = "%locale_api_v_1%", "domain_api_v_1" = "%domain_api_v_1%"},
     *      requirements = {"_locale" = "%locale_api_v_1%", "domain_api_v_1" = "%domain_api_v_1%"}
     * )
     */
    public function getBankingMachinesSyncsAction(Request $request, $serial)
    {
        $bankingMachine = $this->_manager->getRepository('AppBundle:BankingMachine\BankingMachine')
            ->findOneBySerialPrefetchRelated($serial);

        try {
            $bankingMachineSyncType = $this->_structureValidator->getBankingMachineSyncTypeIfValid($request);
        } catch(RuntimeException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $bankingMachineSync = $this->_manager->getRepository('AppBundle:BankingMachine\BankingMachineSync')
            ->findLatestByBankingMachineSyncType($bankingMachine, $bankingMachineSyncType);

        $serialized = $this->_bankingMachineSyncSerializer->syncSerializeObject($bankingMachineSync);

        $formattedData = $this->_formatter->formatRawData($serialized);

        return new Response(
            json_encode($formattedData, JSON_UNESCAPED_UNICODE), 200
        );
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/banking_machines/{serial}",
     *      name = "sync_get_banking_machines",
     *      host = "{domain_api_v_1}",
     *      schemes = {"http"},
     *      defaults = {"_locale" = "%locale_api_v_1%", "domain_api_v_1" = "%domain_api_v_1%"},
     *      requirements = {"_locale" = "%locale_api_v_1%", "domain_api_v_1" = "%domain_api_v_1%"}
     * )
     */
    public function getBankingMachinesAction($serial)
    {
        $bankingMachine = $this->_manager->getRepository('AppBundle:BankingMachine\BankingMachine')
            ->findOneBySerialPrefetchRelated($serial);

        $serialized = $this->_bankingMachineSerializer->syncSerializeObject($bankingMachine);

        $formattedData = $this->_formatter->formatRawData($serialized);

        // $this->_recorder->recordGetBankingMachinesSync($bankingMachine, $formattedData);

        $bankingMachineSync = (new BankingMachineSync)
            ->setSyncType('sync_get_banking_machines')
            ->setSyncAt(new \DateTime)
        ;

        $bankingMachineSync = $this->_handler
            ->handleBankingMachineSyncDataTest($bankingMachine, $bankingMachineSync, $formattedData);

        $this->_manager->flush();

        return new Response(
            json_encode($formattedData, JSON_UNESCAPED_UNICODE), 200
        );
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/banking_machines/{serial}/operators",
     *      name = "sync_get_banking_machines_operators",
     *      host = "{domain_api_v_1}",
     *      schemes = {"http"},
     *      defaults = {"_locale" = "%locale_api_v_1%", "domain_api_v_1" = "%domain_api_v_1%"},
     *      requirements = {"_locale" = "%locale_api_v_1%", "domain_api_v_1" = "%domain_api_v_1%"}
     * )
     */
    public function getBankingMachinesOperatorsAction($serial)
    {
        $bankingMachine = $this->_manager->getRepository('AppBundle:BankingMachine\BankingMachine')
            ->findOneBySerialPrefetchRelated($serial);

        $operators = $this->filterDeleted($bankingMachine->getOperators());

        $serialized = $this->_operatorSerializer->syncSerializeArray($operators);

        $formattedData = $this->_formatter->formatRawData($serialized);

        //$this->_recorder->recordGetBankingMachinesOperators($bankingMachine, $formattedData);

        $bankingMachineSync = (new BankingMachineSync)
            ->setSyncType('sync_get_banking_machines_operators')
            ->setSyncAt(new \DateTime)
        ;

        $bankingMachineSync = $this->_handler
            ->handleBankingMachineSyncDataTest($bankingMachine, $bankingMachineSync, $formattedData);

        $this->_manager->flush();

        return new Response(
            json_encode($formattedData, JSON_UNESCAPED_UNICODE), 200
        );
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/banking_machines/{serial}/account_groups",
     *      name = "sync_get_banking_machines_account_groups",
     *      host = "{domain_api_v_1}",
     *      schemes = {"http"},
     *      defaults = {"_locale" = "%locale_api_v_1%", "domain_api_v_1" = "%domain_api_v_1%"},
     *      requirements = {"_locale" = "%locale_api_v_1%", "domain_api_v_1" = "%domain_api_v_1%"}
     * )
     */
    public function getBankingMachinesAccountGroupsAction($serial)
    {
        $bankingMachine = $this->_manager->getRepository('AppBundle:BankingMachine\BankingMachine')
            ->findOneBySerialPrefetchRelated($serial);

        $accountGroups = $this->filterDeleted($bankingMachine->getAccountGroups());

        $serialized = $this->_accountGroupSerializer->syncSerializeArray($accountGroups);

        $formattedData = $this->_formatter->formatRawData($serialized);

        // $this->_recorder->recordGetBankingMachinesAccountGroups($bankingMachine, $formattedData);

        $bankingMachineSync = (new BankingMachineSync)
            ->setSyncType('sync_get_banking_machines_account_groups')
            ->setSyncAt(new \DateTime)
        ;

        $bankingMachineSync = $this->_handler
            ->handleBankingMachineSyncDataTest($bankingMachine, $bankingMachineSync, $formattedData);

        $this->_manager->flush();

        return new Response(
            json_encode($formattedData, JSON_UNESCAPED_UNICODE), 200
        );
    }

    /**
     * @Method({"POST"})
     * @Route(
     *      "/banking_machines/{serial}/replenishments",
     *      name = "sync_get_banking_machines_replenishments",
     *      host = "{domain_api_v_1}",
     *      schemes = {"http"},
     *      defaults = {"_locale" = "%locale_api_v_1%", "domain_api_v_1" = "%domain_api_v_1%"},
     *      requirements = {"_locale" = "%locale_api_v_1%", "domain_api_v_1" = "%domain_api_v_1%"}
     * )
     */
    public function postBankingMachinesReplenishmentsAction(Request $request, $serial)
    {
        $bankingMachine = $this->_manager->getRepository('AppBundle:BankingMachine\BankingMachine')
            ->findOneBySerialPrefetchRelated($serial);

        try {
            $serializedBankingMachineSync = $this->_structureValidator
                ->getBankingMachineSyncIfValid($request);

            $bankingMachineSync = $this->_bankingMachineSyncSerializer
                ->syncUnserializeObject($serializedBankingMachineSync);

            $bankingMachineSync->setSyncType('sync_get_banking_machines_replenishments');
        } catch(RuntimeException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if( $this->_sequenceValidator->isAlreadyPersisted($bankingMachine, $bankingMachineSync) )
            return new Response('Already in sync', 200);

        try{
            $serializedReplenishments = $this->_structureValidator
                ->getReplenishmentsIfValid($request);

            $replenishments = $this->_replenishmentSerializer
                ->syncUnserializeArray($serializedReplenishments);
        } catch(RuntimeException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $this->_manager->getConnection()->beginTransaction();

        try{
            $bankingMachineSync = $this->_handler
                ->handleBankingMachineSyncDataTest($bankingMachine, $bankingMachineSync);

            $replenishments = $this->_handler
                ->handleReplenishmentDataTest($bankingMachine, $bankingMachineSync, $replenishments);

            $this->_manager->flush();
            $this->_manager->clear();

            $this->_manager->getConnection()->commit();
        }catch( Exception $e ){
            $this->_manager->getConnection()->rollback();

            throw new FatalErrorException('Database Error: ' . $e->getMessage());
        }

        $response = $this->forward('SyncBundle:BankingServer:transfer', [
            'syncId' => $bankingMachineSync->getSyncId(),
        ]);

        return new Response(
            json_encode(['data' => ['transaction-id' => $bankingMachineSync->getSyncId()]], JSON_UNESCAPED_UNICODE), 200
        );
    }
}
