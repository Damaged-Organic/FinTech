<?php
// src/SyncBundle/Service/BankingMachine/Sync/Validator/Structure.php
namespace SyncBundle\Service\BankingMachine\Sync\Validator;

use RuntimeException;

use Symfony\Component\HttpFoundation\Request;

use AppBundle\Serializer\BankingMachineSyncSerializer,
    AppBundle\Serializer\ReplenishmentSerializer,
    AppBundle\Serializer\CollectionSerializer;

use SyncBundle\Service\BankingMachine\Sync\Utility\ChecksumCalculator;

class Structure
{
    private $_checksumCalculator;

    public function setChecksumCalculator(ChecksumCalculator $checksumCalculator)
    {
        $this->_checksumCalculator = $checksumCalculator;
    }

    private function getRequestContentIfValid(Request $request)
    {
        $requestContent = json_decode($request->getContent(), TRUE);

        $checksumPropertyName = BankingMachineSyncSerializer::getChecksumPropertyName();
        $dataPropertyName     = BankingMachineSyncSerializer::getDataPropertyName();

        if( empty($requestContent[$checksumPropertyName]) ||
            empty($requestContent[$dataPropertyName]) )
            return FALSE;

        return [
            $requestContent[$checksumPropertyName],
            $requestContent[$dataPropertyName]
        ];
    }

    private function validateChecksum($checksum, $data)
    {
        if( !$this->_checksumCalculator->verifyDataChecksum($checksum, $data) )
            return FALSE;

        return TRUE;
    }

    private function getDataIfValid(Request $request)
    {
        if( !($requestContent = $this->getRequestContentIfValid($request)) )
            throw new RuntimeException('Initial data structure mismatch');

        list($checksum, $data) = $requestContent;

        if( !$this->validateChecksum($checksum, $data) )
            throw new RuntimeException('Data checksum hash mismatch');

        return $data;
    }

    public function getBankingMachineSyncTypeIfValid(Request $request)
    {
        $syncTypePropertyName = BankingMachineSyncSerializer::getSyncTypePropertyName();

        if( !$request->query->has($syncTypePropertyName) )
            throw new RuntimeException('Sync data property mismatch');

        return $request->query->get($syncTypePropertyName);
    }

    private function getBankingMachineSync($checksum, $data)
    {
        $bankingMachineSyncObjectName = BankingMachineSyncSerializer::getObjectName();

        if( empty($data[$bankingMachineSyncObjectName]) )
            return FALSE;

        if( !is_array($data[$bankingMachineSyncObjectName]) )
            return FALSE;

        $syncIdPropertyName = BankingMachineSyncSerializer::getSyncIdPropertyName();
        $syncAtPropertyName = BankingMachineSyncSerializer::getSyncAtPropertyName();

        if( empty($data[$bankingMachineSyncObjectName][$syncIdPropertyName]) ||
            empty($data[$bankingMachineSyncObjectName][$syncAtPropertyName]) )
            return FALSE;

        $checksumPropertyName = BankingMachineSyncSerializer::getChecksumPropertyName();
        $dataPropertyName     = BankingMachineSyncSerializer::getDataPropertyName();

        $data[$bankingMachineSyncObjectName][$checksumPropertyName] = $checksum;
        $data[$bankingMachineSyncObjectName][$dataPropertyName]     = $data;

        return $data[$bankingMachineSyncObjectName];
    }

    public function getBankingMachineSyncIfValid(Request $request)
    {
        $data = $this->getDataIfValid($request);

        if( !($requestContent = $this->getRequestContentIfValid($request)) )
            throw new RuntimeException('Initial data structure mismatch');

        list($checksum, $data) = $requestContent;

        if( !($bankingMachineSync = $this->getBankingMachineSync($checksum, $data)) )
            throw new RuntimeException('Sync data structure mismatch');

        return $bankingMachineSync;
    }

    private function getReplenishments($data)
    {
        $replenishmentsArrayName = ReplenishmentSerializer::getArrayName();

        if( empty($data[$replenishmentsArrayName]) )
            return FALSE;

        if( !is_array($data[$replenishmentsArrayName]) )
            return FALSE;

        return $data[$replenishmentsArrayName];
    }

    public function getReplenishmentsIfValid(Request $request)
    {
        $data = $this->getDataIfValid($request);

        if( !($replenishments = $this->getReplenishments($data)) )
            throw new RuntimeException('Replenishment data structure mismatch');

        return $replenishments;
    }

    private function getCollections($data)
    {
        $collectionsArrayName = CollectionSerializer::getArrayName();

        if( empty($data[$collectionsArrayName]) )
            return FALSE;

        if( !is_array($data[$collectionsArrayName]) )
            return FALSE;

        return $data[$collectionsArrayName];
    }

    public function getCollectionsIfValid(Request $request)
    {
        $data = $this->getDataIfValid($request);

        if( !($collections = $this->getCollections($data)) )
            throw new RuntimeException('Collection data structure mismatch');

        return $collections;
    }
}
