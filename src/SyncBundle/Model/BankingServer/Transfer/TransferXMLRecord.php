<?php
// src/SyncBundle/Model/BankingServer/Transfer/TransferXMLRecord.php
namespace SyncBundle\Model\BankingServer\Transfer;

use AppBundle\Entity\Account\Account,
    AppBundle\Entity\Transaction\Transaction;

// TODO: No XML formatter for now, not implementing interface
class TransferXMLRecord
{
    // String fields encoding
    const ENCODING = 'windows-1251';

    // Files specific newline character
    const NEWLINE = "\r\n";

    private $transactionEntity;
    private $transferRecord;

    public function __construct(
        Transaction $transactionEntity,
        Account $transferRecordEntity
    ) {
        $this->transaction    = $transactionEntity;
        $this->transferRecord = $transferRecordEntity;
    }

    /*-------------------------------------------------------------------------
    | Transfer Record Getters
    |------------------------------------------------------------------------*/

    /**
     * Get bankingMachine serial
     *
     * @return \string
     */
    public function getBankingMachineSerial()
    {
        return mb_convert_encoding(
            (string)$this->transaction->getBankingMachine()->getSerial(), self::ENCODING, 'UTF-8'
        );
    }

    /**
     * Get bankingMachine address
     *
     * @return \string
     */
    public function getBankingMachineAddress()
    {
        return mb_convert_encoding(
            (string)$this->transaction->getBankingMachine()->getAddress(), self::ENCODING, 'UTF-8'
        );
    }

    /**
     * Get paymentDestination
     *
     * @return \string
     */
    public function getPaymentDestination()
    {
        return mb_convert_encoding(
            (string)$this->transferRecord->getPaymentDestination(), self::ENCODING, 'UTF-8'
        );
    }

    /**
     * Get paymentAmount
     *
     * @return \string
     */
    public function getPaymentAmount()
    {
        $paymentAmount = bcmul($this->transferRecord->getPaymentAmount(), 100);

        return (string)$paymentAmount;
    }

    /**
     * Get paymentCurrency
     *
     * @return \string
     */
    public function getPaymentCurrency()
    {
        return mb_convert_encoding(
            (string)$this->transferRecord->getPaymentCurrency(), self::ENCODING, 'UTF-8'
        );
    }

    /**
     * Get paymentDocumentDate
     *
     * @return \string
     */
    public function getPaymentDocumentDate()
    {
        return mb_convert_encoding(
            (string)$this->transferRecord->getPaymentDocumentDate()->format('ymd'), self::ENCODING, 'UTF-8'
        );
    }

    /**
     * Get NfcTag paymentDocumentDate
     *
     * @return \string
     */
    public function getNfcTagNumber()
    {
        return mb_convert_encoding(
            (string)$this->transaction->getOperator()->getNfcTag()->getNumber(), self::ENCODING, 'UTF-8'
        );
    }

    /**
     * Get Operator fullName
     *
     * @return \string
     */
    public function getOperatorFullName()
    {
        return mb_convert_encoding(
            (string)$this->transaction->getOperator()->getFullName(), self::ENCODING, 'UTF-8'
        );
    }

    /*-------------------------------------------------------------------------
    | End | Transfer Record Getters
    |------------------------------------------------------------------------*/

    public function getTransferRecordRow()
    {
        return implode(';', [
            $this->getBankingMachineSerial(),
            $this->getBankingMachineAddress(),
            $this->getPaymentDestination(),
            $this->getPaymentAmount(),
            $this->getPaymentCurrency(),
            $this->getPaymentDocumentDate(),
            $this->getNfcTagNumber(),
            $this->getOperatorFullName(),
        ]) . self::NEWLINE;
    }
}
