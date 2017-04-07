<?php
// src/SyncBundle/Model/BankingServer/Transfer/TransferRecord.php
namespace SyncBundle\Model\BankingServer\Transfer;

use AppBundle\Entity\Account\Account,
    AppBundle\Entity\Account\Utility\Interfaces\AccountAttributesInterface;

use SyncBundle\Service\BankingServer\Transfer\Formatter;

class TransferRecord implements AccountAttributesInterface
{
    // String fields encoding
    const ENCODING = 'windows-1251';

    // Files specific newline character
    const NEWLINE = "\r\n";

    private $transferRecord;
    private $formatter;

    public function __construct(
        Account $transferRecordEntity,
        Formatter $formatter
    ) {
        $this->transferRecord = $transferRecordEntity;
        $this->formatter      = $formatter;
    }

    /*-------------------------------------------------------------------------
    | Transfer Record Getters
    |------------------------------------------------------------------------*/

    /**
     * Get mfoOffBankA
     *
     * @return \number
     */
    public function getMfoOfBankA()
    {
        return $this->formatter->formatRecordField(
            $this->transferRecord->getMfoOfBankA(),
            self::MFO_OF_BANK_A_LENGTH
        );
    }

    /**
     * Get personalAccountOfBankA
     *
     * @return \number
     */
    public function getPersonalAccountOfBankA()
    {
        return $this->formatter->formatRecordField(
            $this->transferRecord->getPersonalAccountOfBankA(),
            self::PERSONAL_ACCOUNT_OF_BANK_A_LENGTH,
            NULL,
            $isBigint=TRUE
        );
    }

    /**
     * Get mfoOfBankB
     *
     * @return \number
     */
    public function getMfoOfBankB()
    {
        return $this->formatter->formatRecordField(
            $this->transferRecord->getMfoOfBankB(),
            self::MFO_OF_BANK_B_LENGTH
        );
    }

    /**
     * Get personalAccountOfBankB
     *
     * @return \number
     */
    public function getPersonalAccountOfBankB()
    {
        return $this->formatter->formatRecordField(
            $this->transferRecord->getPersonalAccountOfBankB(),
            self::PERSONAL_ACCOUNT_OF_BANK_B_LENGTH,
            NULL,
            $isBigint=TRUE
        );
    }

    /**
     * Get debitCreditPaymentFlag
     *
     * @return boolean
     */
    public function getDebitCreditPaymentFlag()
    {
        return $this->formatter->formatRecordField(
            ( $this->transferRecord->getDebitCreditPaymentFlag() ) ? 1 : 0,
            self::DEBIT_CREDIT_PAYMENT_FLAG_LENGTH
        );
    }

    /**
     * Get paymentAmount
     *
     * @return \number
     */
    public function getPaymentAmount()
    {
        $paymentAmount = bcmul($this->transferRecord->getPaymentAmount(), 100);

        return $this->formatter->formatRecordField(
            $paymentAmount,
            self::PAYMENT_AMOUNT_LENGTH,
            NULL,
            $isBigint=TRUE
        );
    }

    /**
     * Get paymentDocumentType
     *
     * @return \number
     */
    public function getPaymentDocumentType()
    {
        return $this->formatter->formatRecordField(
            $this->transferRecord->getPaymentDocumentType(),
            self::PAYMENT_DOCUMENT_TYPE_LENGTH
        );
    }

    /**
     * Get paymentOperationalNumber
     *
     * @return string
     */
    public function getPaymentOperationalNumber()
    {
        return $this->formatter->formatRecordField(
            $this->transferRecord->getPaymentOperationalNumber(),
            self::PAYMENT_OPERATIONAL_NUMBER_LENGTH,
            self::ENCODING,
            $isBigint=TRUE
        );
    }

    /**
     * Get paymentCurrency
     *
     * @return \number
     */
    public function getPaymentCurrency()
    {
        return $this->formatter->formatRecordField(
            $this->transferRecord->getPaymentCurrency(),
            self::PAYMENT_CURRENCY_LENGTH
        );
    }

    /**
     * Get paymentDocumentDate
     *
     * @return \DateTime
     */
    public function getPaymentDocumentDate()
    {
        return $this->formatter->formatRecordField(
            $this->transferRecord->getPaymentDocumentDate()->format('ymd'),
            self::PAYMENT_DOCUMENT_DATE_LENGTH
        );
    }

    /**
     * Get paymentDocumentArrivalDateToBankA
     *
     * @return \DateTime
     */
    public function getPaymentDocumentArrivalDateToBankA()
    {
        return $this->formatter->formatRecordField(
            $this->transferRecord->getPaymentDocumentArrivalDateToBankA()->format('ymd'),
            self::PAYMENT_DOCUMENT_ARRIVAL_DATE_TO_BANK_A_LENGTH
        );
    }

    /**
     * Get payerNameOfClientA
     *
     * @return string
     */
    public function getPayerNameOfClientA()
    {
        return $this->formatter->formatRecordField(
            $this->transferRecord->getPayerNameOfClientA(),
            self::PAYER_NAME_OF_CLIENT_A_LENGTH,
            self::ENCODING
        );
    }

    /**
     * Get payerNameOfClientB
     *
     * @return string
     */
    public function getPayerNameOfClientB()
    {
        return $this->formatter->formatRecordField(
            $this->transferRecord->getPayerNameOfClientB(),
            self::PAYER_NAME_OF_CLIENT_B_LENGTH,
            self::ENCODING
        );
    }

    /**
     * Get paymentDestination
     *
     * @return string
     */
    public function getPaymentDestination()
    {
        return $this->formatter->formatRecordField(
            $this->transferRecord->getPaymentDestination(),
            self::PAYMENT_DESTINATION_LENGTH,
            self::ENCODING
        );
    }

    /**
     * Get supportingProps
     *
     * @return string
     */
    public function getSupportingProps()
    {
        return $this->formatter->formatRecordField(
            $this->transferRecord->getSupportingProps(),
            self::SUPPORTING_PROPS_LENGTH,
            self::ENCODING
        );
    }

    /**
     * Get paymentDestinationCode
     *
     * @return string
     */
    public function getPaymentDestinationCode()
    {
        return $this->formatter->formatRecordField(
            $this->transferRecord->getPaymentDestinationCode(),
            self::PAYMENT_DESTINATION_CODE_LENGTH,
            self::ENCODING
        );
    }

    /**
     * Get stringsNumberInBlock
     *
     * @return string
     */
    public function getStringsNumberInBlock()
    {
        return $this->formatter->formatRecordField(
            $this->transferRecord->getStringsNumberInBlock(),
            self::STRINGS_NUMBER_IN_BLOCK_LENGTH,
            self::ENCODING
        );
    }

    /**
     * Get clientIdentifierA
     *
     * @return string
     */
    public function getClientIdentifierA()
    {
        return $this->formatter->formatRecordField(
            $this->transferRecord->getClientIdentifierA(),
            self::CLIENT_IDENTIFIER_A_LENGTH,
            self::ENCODING
        );
    }

    /**
     * Get clientIdentifierB
     *
     * @return string
     */
    public function getClientIdentifierB()
    {
        return $this->formatter->formatRecordField(
            $this->transferRecord->getClientIdentifierB(),
            self::CLIENT_IDENTIFIER_B_LENGTH,
            self::ENCODING
        );
    }

    /*-------------------------------------------------------------------------
    | End | Transfer Record Getters
    |------------------------------------------------------------------------*/

    public function getTransferRecordRow()
    {
        return $this->formatter->formatRecordString([
            $this->getMfoOfBankA(),
            $this->getPersonalAccountOfBankA(),
            $this->getMfoOfBankB(),
            $this->getPersonalAccountOfBankB(),
            $this->getDebitCreditPaymentFlag(),
            $this->getPaymentAmount(),
            $this->getPaymentDocumentType(),
            $this->getPaymentOperationalNumber(),
            $this->getPaymentCurrency(),
            $this->getPaymentDocumentDate(),
            $this->getPaymentDocumentArrivalDateToBankA(),
            $this->getPayerNameOfClientA(),
            $this->getPayerNameOfClientB(),
            $this->getPaymentDestination(),
            $this->getSupportingProps(),
            $this->getPaymentDestinationCode(),
            $this->getStringsNumberInBlock(),
            $this->getClientIdentifierA(),
            $this->getClientIdentifierB(),
        ], self::NEWLINE);
    }
}
