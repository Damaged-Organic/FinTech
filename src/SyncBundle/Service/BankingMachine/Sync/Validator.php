<?php
// src/SyncBundle/Service/BankingMachine/Sync/Validator.php
namespace SyncBundle\Service\BankingMachine\Sync;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\Validator\Constraints as Assert,
    Symfony\Component\Validator\Validator\ValidatorInterface,
    Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

use AppBundle\Validator\Constraints as CustomAssert,
    AppBundle\Entity\Transaction\Replenishment,
    AppBundle\Serializer\ReplenishmentSerializer,
    AppBundle\Entity\Operator\Operator,
    AppBundle\Serializer\OperatorSerializer,
    AppBundle\Entity\Account\AccountGroup,
    AppBundle\Serializer\AccountGroupSerializer,
    AppBundle\Entity\Banknote\Banknote,
    AppBundle\Serializer\BanknoteSerializer,
    AppBundle\Entity\Banknote\BanknoteList,
    AppBundle\Serializer\BanknoteListSerializer;

use SyncBundle\Service\BankingMachine\Sync\Interfaces\SyncDataInterface,
    SyncBundle\Service\BankingMachine\Sync\Utility\ChecksumCalculator;

class Validator implements SyncDataInterface
{
    private $_validator;
    private $_checksumCalculator;

    public function setValidator(ValidatorInterface $validator)
    {
        $this->_validator = $validator;
    }

    public function setChecksumCalculator(ChecksumCalculator $checksumCalculator)
    {
        $this->_checksumCalculator = $checksumCalculator;
    }

    private function getRequestContentData(array $requestContent = NULL)
    {
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

    private function validateSyncData($data)
    {
        $syncArrayName = 'sync';

        if( empty($data[$syncArrayName]) )
            return FALSE;

        if( !is_array($data[$syncArrayName]) )
            return FALSE;

        if( empty($data[$syncArrayName]['id']) || empty($data[$syncArrayName]['at']) )
            return FALSE;

        return $data[$syncArrayName];
    }

    private function getReplenishmentsData($data)
    {
        $replenishmentsArrayName = ReplenishmentSerializer::getArrayName();

        if( empty($data[$replenishmentsArrayName]) )
            return FALSE;

        if( !is_array($data[$replenishmentsArrayName]) )
            return FALSE;

        return $data[$replenishmentsArrayName];
    }

    public function validateReplenishmentData(Request $request)
    {
        $requestContent = json_decode($request->getContent(), TRUE);

        if( !($requestContentData = $this->getRequestContentData($requestContent)) )
            throw new BadRequestHttpException('Initial data structure mismatch');

        list($checksum, $data) = $requestContentData;

        if( !$this->validateChecksum($checksum, $data) )
            throw new BadRequestHttpException('Data checksum hash mismatch');

        if( !$this->validateSyncData($data) )
            throw new BadRequestHttpException('Sync data structure mismatch');

        if( !($replenishmentsData = $this->getReplenishmentsData($data)) )
            throw new BadRequestHttpException('Replenishment data structure mismatch');

        $assertDateTime = new Assert\DateTime;
        $assertIsPrice  = new CustomAssert\IsDecimal;

        foreach( $replenishmentsData as $replenishmentData )
        {
            if( empty($replenishmentData[Replenishment::PROPERTY_TRANSACTION_AT]) )
                return FALSE;
            $datetimeErrors = count($this->_validator->validate(
                $replenishmentData[Replenishment::PROPERTY_TRANSACTION_AT],
                $assertDateTime
            ));
            if( $datetimeErrors !== 0 )
                return FALSE;

            # Operator

            $operatorObjectName = OperatorSerializer::getObjectName();

            if( empty($replenishmentData[$operatorObjectName]) ||
                !is_array($replenishmentData[$operatorObjectName]) )
                return FALSE;

            if( empty($replenishmentData[$operatorObjectName][Operator::PROPERTY_ID]) )
                return FALSE;

            if( empty($replenishmentData[$operatorObjectName][Operator::PROPERTY_FULL_NAME]) )
                return FALSE;

            # Account Group

            $accountGroupObjectName = AccountGroupSerializer::getObjectName();

            if( empty($replenishmentData[$accountGroupObjectName]) ||
                !is_array($replenishmentData[$accountGroupObjectName]) )
                return FALSE;

            if( empty($replenishmentData[$accountGroupObjectName][AccountGroup::PROPERTY_ID]) )
                return FALSE;

            if( empty($replenishmentData[$accountGroupObjectName][AccountGroup::PROPERTY_NAME]) )
                return FALSE;

            # Banknote

            $banknoteArrayName = BanknoteSerializer::getArrayName();

            if( empty($replenishmentData[$banknoteArrayName]) ||
                !is_array($replenishmentData[$banknoteArrayName]) )
                return FALSE;

            foreach( $replenishmentData[$banknoteArrayName] as $banknote )
            {
                if( empty($banknote[Banknote::PROPERTY_CURRENCY]) )
                    return FALSE;

                if( empty($banknote[Banknote::PROPERTY_NOMINAL]) )
                    return FALSE;
                $isPriceErrors = count($this->_validator->validate(
                    $banknote[Banknote::PROPERTY_NOMINAL],
                    $assertIsPrice
                ));
                if( $isPriceErrors !== 0 )
                    return FALSE;

                if( empty($banknote[BanknoteList::PROPERTY_QUANTITY]) )
                    return FALSE;
            }
        }

        return $requestContent;
    }
}
