<?php
// src/SyncBundle/Service/BankingMachine/Sync/Validator/Structure.php
namespace SyncBundle\Service\BankingMachine\Sync\Validator;

use Symfony\Component\HttpFoundation\Request;

use AppBundle\Serializer\ReplenishmentSerializer;

use SyncBundle\Service\BankingMachine\Sync\Interfaces\SyncDataInterface,
    SyncBundle\Service\BankingMachine\Sync\Utility\ChecksumCalculator;

class Structure implements SyncDataInterface
{
    private $_checksumCalculator;

    public function setChecksumCalculator(ChecksumCalculator $checksumCalculator)
    {
        $this->_checksumCalculator = $checksumCalculator;
    }

    private function getRequestContentIfValid(Request $request)
    {
        $requestContent = json_decode($request->getContent(), TRUE);

        if( empty($requestContent[self::SYNC_CHECKSUM]) || empty($requestContent[self::SYNC_DATA]) )
            return FALSE;

        return [
            $requestContent[self::SYNC_CHECKSUM],
            $requestContent[self::SYNC_DATA]
        ];
    }

    private function validateChecksum($checksum, $data)
    {
        if( !$this->_checksumCalculator->verifyDataChecksum($checksum, $data) )
            return FALSE;

        return TRUE;
    }

    private function validateSync($data)
    {
        // TODO: Link this to BankingMachineSync
        $syncArrayName = 'sync';

        if( empty($data[$syncArrayName]) )
            return FALSE;

        if( !is_array($data[$syncArrayName]) )
            return FALSE;

        if( empty($data[$syncArrayName]['id']) || empty($data[$syncArrayName]['at']) )
            return FALSE;

        return TRUE;
    }

    private function getDataIfValid(Request $request)
    {
        if( !($requestContent = $this->getRequestContentIfValid($request)) )
            throw new BadRequestHttpException('Initial data structure mismatch');

        list($checksum, $data) = $requestContent;

        if( !$this->validateChecksum($checksum, $data) )
            throw new BadRequestHttpException('Data checksum hash mismatch');

        if( !$this->validateSync($data) )
            throw new BadRequestHttpException('Data structure mismatch');

        return $data;
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
            throw new BadRequestHttpException('Replenishment data structure mismatch');

        return $replenishments;
    }

    private function getCollections($data)
    {

    }

    public function getCollectionsIfValid(Request $request)
    {
        
    }
}
