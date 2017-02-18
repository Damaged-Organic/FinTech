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

    const SYNC_GET_BANKING_MACHINES_SYNCS           = 'sync_get_banking_machines_syncs';
    const SYNC_GET_BANKING_MACHINES                 = 'sync_get_banking_machines';
    const SYNC_GET_BANKING_MACHINES_OPERATORS       = 'sync_get_banking_machines_operators';
    const SYNC_GET_BANKING_MACHINES_ACCOUNT_GROUPS  = 'sync_get_banking_machines_account_groups';
    const SYNC_POST_BANKING_MACHINES_REPLENISHMENTS = 'sync_post_banking_machines_replenishments';
    const SYNC_POST_BANKING_MACHINES_COLLECTIONS    = 'sync_post_banking_machines_collections';
    const SYNC_POST_BANKING_MACHINES_EVENTS         = 'sync_post_banking_machines_events';

    /** @DI\Inject("doctrine.orm.entity_manager") */
    private $_manager;

    /** @DI\Inject("sync.banking_machine.sync.validator.structure") */
    private $_syncStructureValidator;

    /** @DI\Inject("sync.banking_machine.sync.validator.sequence") */
    private $_syncSequenceValidator;

    /** @DI\Inject("sync.banking_machine.sync.manager") */
    private $_syncManager;

    /** @DI\Inject("sync.banking_machine.sync.formatter") */
    private $_syncFormatter;

    /** @DI\Inject("app.serializer.banking_machine") */
    private $_bankingMachineSerializer;

    /** @DI\Inject("app.serializer.banking_machine_sync") */
    private $_bankingMachineSyncSerializer;

    /** @DI\Inject("app.serializer.banking_machine_event") */
    private $_bankingMachineEventSerializer;

    /** @DI\Inject("app.serializer.operator") */
    private $_operatorSerializer;

    /** @DI\Inject("app.serializer.account_group") */
    private $_accountGroupSerializer;

    /** @DI\Inject("app.serializer.replenishment") */
    private $_replenishmentSerializer;

    /** @DI\Inject("app.serializer.collection") */
    private $_collectionSerializer;

    /**
     * @Method({"GET"})
     * @Route(
     *      "/banking_machines/{serial}/syncs",
     *      name = BankingMachineController::SYNC_GET_BANKING_MACHINES_SYNCS,
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
            $bankingMachineSyncType = $this->_syncStructureValidator
                ->getBankingMachineSyncTypeIfValid($request);
        } catch(RuntimeException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $bankingMachineSync = $this->_manager->getRepository('AppBundle:BankingMachine\BankingMachineSync')
            ->findLatestByBankingMachineSyncType($bankingMachine, $bankingMachineSyncType);

        $serialized = $this->_bankingMachineSyncSerializer->syncSerializeObject($bankingMachineSync);

        $bankingMachineSync = $this->_syncFormatter
            ->getExportBankingMachineSync(self::SYNC_GET_BANKING_MACHINES_SYNCS, $serialized);

        return new Response(
            $this->_syncFormatter->formatSyncData($bankingMachineSync), 200
        );
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/banking_machines/{serial}",
     *      name = BankingMachineController::SYNC_GET_BANKING_MACHINES,
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

        $bankingMachineSync = $this->_syncFormatter
            ->getExportBankingMachineSync(self::SYNC_GET_BANKING_MACHINES, $serialized);

        $bankingMachineSync = $this->_syncManager
            ->persistBankingMachineSync($bankingMachine, $bankingMachineSync);

        $this->_manager->flush();

        return new Response(
            $this->_syncFormatter->formatSyncData($bankingMachineSync), 200
        );
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/banking_machines/{serial}/operators",
     *      name = BankingMachineController::SYNC_GET_BANKING_MACHINES_OPERATORS,
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

        $bankingMachineSync = $this->_syncFormatter
            ->getExportBankingMachineSync(self::SYNC_GET_BANKING_MACHINES_OPERATORS, $serialized);

        $bankingMachineSync = $this->_syncManager
            ->persistBankingMachineSync($bankingMachine, $bankingMachineSync);

        $this->_manager->flush();

        return new Response(
            $this->_syncFormatter->formatSyncData($bankingMachineSync), 200
        );
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/banking_machines/{serial}/account_groups",
     *      name = BankingMachineController::SYNC_GET_BANKING_MACHINES_ACCOUNT_GROUPS,
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

        $bankingMachineSync = $this->_syncFormatter
            ->getExportBankingMachineSync(self::SYNC_GET_BANKING_MACHINES_ACCOUNT_GROUPS, $serialized);

        $bankingMachineSync = $this->_syncManager
            ->persistBankingMachineSync($bankingMachine, $bankingMachineSync);

        $this->_manager->flush();

        return new Response(
            $this->_syncFormatter->formatSyncData($bankingMachineSync), 200
        );
    }

    /**
     * @Method({"POST"})
     * @Route(
     *      "/banking_machines/{serial}/replenishments",
     *      name = BankingMachineController::SYNC_POST_BANKING_MACHINES_REPLENISHMENTS,
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
            $serializedBankingMachineSync = $this->_syncStructureValidator
                ->getBankingMachineSyncIfValid($request);

            $bankingMachineSync = $this->_bankingMachineSyncSerializer
                ->syncUnserializeObject($serializedBankingMachineSync);

            $bankingMachineSync = $this->_syncFormatter
                ->getImportBankingMachineSync(self::SYNC_POST_BANKING_MACHINES_REPLENISHMENTS, $bankingMachineSync);
        } catch(RuntimeException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if( $this->_syncSequenceValidator->isAlreadyPersisted($bankingMachine, $bankingMachineSync) )
            return new Response('Already in sync', 200);

        try{
            $serializedReplenishments = $this->_syncStructureValidator
                ->getReplenishmentsIfValid($request);

            $replenishments = $this->_replenishmentSerializer
                ->syncUnserializeArray($serializedReplenishments);
        } catch(RuntimeException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $this->_manager->getConnection()->beginTransaction();

        try{
            $bankingMachineSync = $this->_syncManager
                ->persistBankingMachineSync($bankingMachine, $bankingMachineSync);

            $syncId = $bankingMachineSync->getSyncId();

            $replenishments = $this->_syncManager
                ->persistReplenishments($bankingMachine, $bankingMachineSync, $replenishments);

            $this->_manager->flush();
            $this->_manager->clear();

            $this->_manager->getConnection()->commit();
        }catch( Exception $e ){
            $this->_manager->getConnection()->rollback();

            throw new FatalErrorException('Database Error: ' . $e->getMessage());
        }

        $response = $this->forward('SyncBundle:BankingServer:transfer', [
            'syncId' => $syncId,
        ]);

        $bankingMachineSync = $this->_syncFormatter
            ->getExportBankingMachineSync(NULL, ['transaction-id' => $syncId]);

        return new Response(
            $this->_syncFormatter->formatSyncData($bankingMachineSync), 200
        );
    }

    /**
     * @Method({"POST"})
     * @Route(
     *      "/banking_machines/{serial}/collections",
     *      name = BankingMachineController::SYNC_POST_BANKING_MACHINES_COLLECTIONS,
     *      host = "{domain_api_v_1}",
     *      schemes = {"http"},
     *      defaults = {"_locale" = "%locale_api_v_1%", "domain_api_v_1" = "%domain_api_v_1%"},
     *      requirements = {"_locale" = "%locale_api_v_1%", "domain_api_v_1" = "%domain_api_v_1%"}
     * )
     */
    public function postBankingMachinesCollectionsAction(Request $request, $serial)
    {
        $bankingMachine = $this->_manager->getRepository('AppBundle:BankingMachine\BankingMachine')
            ->findOneBySerialPrefetchRelated($serial);

        try {
            $serializedBankingMachineSync = $this->_syncStructureValidator
                ->getBankingMachineSyncIfValid($request);

            $bankingMachineSync = $this->_bankingMachineSyncSerializer
                ->syncUnserializeObject($serializedBankingMachineSync);

            $bankingMachineSync = $this->_syncFormatter
                ->getImportBankingMachineSync(self::SYNC_POST_BANKING_MACHINES_COLLECTIONS, $bankingMachineSync);
        } catch(RuntimeException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if( $this->_syncSequenceValidator->isAlreadyPersisted($bankingMachine, $bankingMachineSync) )
            return new Response('Already in sync', 200);

        try{
            $serializedCollections = $this->_syncStructureValidator
                ->getCollectionsIfValid($request);

            $collections = $this->_collectionSerializer
                ->syncUnserializeArray($serializedCollections);
        } catch(RuntimeException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $this->_manager->getConnection()->beginTransaction();

        try{
            $bankingMachineSync = $this->_syncManager
                ->persistBankingMachineSync($bankingMachine, $bankingMachineSync);

            $syncId = $bankingMachineSync->getSyncId();

            $collections = $this->_syncManager
                ->persistCollections($bankingMachine, $bankingMachineSync, $collections);

            $this->_manager->flush();
            $this->_manager->clear();

            $this->_manager->getConnection()->commit();
        }catch( Exception $e ){
            $this->_manager->getConnection()->rollback();

            throw new FatalErrorException('Database Error: ' . $e->getMessage());
        }

        $response = $this->forward('SyncBundle:BankingServer:transfer', [
            'syncId' => $syncId,
        ]);

        $bankingMachineSync = $this->_syncFormatter
            ->getExportBankingMachineSync(NULL, ['transaction-id' => $syncId]);

        return new Response(
            $this->_syncFormatter->formatSyncData($bankingMachineSync), 200
        );
    }

    /**
     * @Method({"POST"})
     * @Route(
     *      "/banking_machines/{serial}/events",
     *      name = BankingMachineController::SYNC_POST_BANKING_MACHINES_EVENTS,
     *      host = "{domain_api_v_1}",
     *      schemes = {"http"},
     *      defaults = {"_locale" = "%locale_api_v_1%", "domain_api_v_1" = "%domain_api_v_1%"},
     *      requirements = {"_locale" = "%locale_api_v_1%", "domain_api_v_1" = "%domain_api_v_1%"}
     * )
     */
    public function postBankingMachinesEventsAction(Request $request, $serial)
    {
        $bankingMachine = $this->_manager->getRepository('AppBundle:BankingMachine\BankingMachine')
            ->findOneBySerialPrefetchRelated($serial);

        try {
            $serializedBankingMachineSync = $this->_syncStructureValidator
                ->getBankingMachineSyncIfValid($request);

            $bankingMachineSync = $this->_bankingMachineSyncSerializer
                ->syncUnserializeObject($serializedBankingMachineSync);

            $bankingMachineSync = $this->_syncFormatter
                ->getImportBankingMachineSync(self::SYNC_POST_BANKING_MACHINES_EVENTS, $bankingMachineSync);
        } catch(RuntimeException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if( $this->_syncSequenceValidator->isAlreadyPersisted($bankingMachine, $bankingMachineSync) )
            return new Response('Already in sync', 200);

        try{
            $serializedBankingMachineEvents = $this->_syncStructureValidator
                ->getBankingMachineEventsIfValid($request);

            $bankingMachineEvents = $this->_bankingMachineEventSerializer
                ->syncUnserializeArray($serializedBankingMachineEvents);
        } catch(RuntimeException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $this->_manager->getConnection()->beginTransaction();

        try{
            $bankingMachineSync = $this->_syncManager
                ->persistBankingMachineSync($bankingMachine, $bankingMachineSync);

            $bankingMachineEvents = $this->_syncManager
                ->persistBankingMachineEvents($bankingMachine, $bankingMachineSync, $bankingMachineEvents);

            $this->_manager->flush();
            $this->_manager->clear();

            $this->_manager->getConnection()->commit();
        }catch( Exception $e ){
            $this->_manager->getConnection()->rollback();

            throw new FatalErrorException('Database Error: ' . $e->getMessage());
        }

        return new Response(NULL, 200);
    }
}
