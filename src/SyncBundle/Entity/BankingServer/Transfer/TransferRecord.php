<?php
// src/SyncBundle/Entity/BankingServer/Transfer/TransferRecord.php
namespace SyncBundle\Entity\BankingServer\Transfer;

use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

use SyncBundle\Entity\BankingServer\Transfer\Utility\Interfaces\TransferRecordFieldsAttributesInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name = "transfers_records")
 */
class TransferRecord implements TransferRecordFieldsAttributesInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    protected $transferId;

    /**
     * @ORM\OneToOne(targetEntity="TransferFile", inversedBy="transferRecord", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="transfer_file_id", referencedColumnName="id")
     */
    protected $transferFile;

    /**
     * [1]
     *
     * @ORM\Column(
     *      type   = TransferRecord::MFO_OF_BANK_A_TYPE,
     *      length = TransferRecord::MFO_OF_BANK_A_LENGTH
     * )
     *
     * @Assert\NotBlank(message = "sync.transfer.mfo_of_bank_a.not_blank")
     * @Assert\Range(
     *      max        = TransferRecord::MFO_OF_BANK_A_LENGTH,
     *      maxMessage = "sync.transfer.mfo_of_bank_a.range.max"
     * )
     */
    protected $mfoOffBankA;

    /**
     * [2]
     *
     * @ORM\Column(
     *      type   = TransferRecord::PERSONAL_ACCOUNT_OF_BANK_A_TYPE,
     *      length = TransferRecord::PERSONAL_ACCOUNT_OF_BANK_A_LENGTH
     * )
     *
     * @Assert\NotBlank(message = "sync.transfer.personal_account_of_bank_a.not_blank")
     * @Assert\Range(
     *      max        = TransferRecord::PERSONAL_ACCOUNT_OF_BANK_A_LENGTH,
     *      maxMessage = "sync.transfer.personal_account_of_bank_a.range.max"
     * )
     */
    protected $personalAccountOfBankA;

    /**
     * [3]
     *
     * @ORM\Column(
     *      type   = TransferRecord::MFO_OF_BANK_B_TYPE,
     *      length = TransferRecord::MFO_OF_BANK_B_LENGTH
     * )
     *
     * @Assert\NotBlank(message = "sync.transfer.mfo_of_bank_b.not_blank")
     * @Assert\Range(
     *      max        = TransferRecord::MFO_OF_BANK_B_LENGTH,
     *      maxMessage = "sync.transfer.mfo_of_bank_b.range.max"
     * )
     */
    protected $mfoOfBankB;

    /**
     * [4]
     *
     * @ORM\Column(
     *      type   = TransferRecord::PERSONAL_ACCOUNT_OF_BANK_B_TYPE,
     *      length = TransferRecord::PERSONAL_ACCOUNT_OF_BANK_B_LENGTH
     * )
     *
     * @Assert\NotBlank(message = "sync.transfer.personal_account_of_bank_b.not_blank")
     * @Assert\Range(
     *      max        = TransferRecord::PERSONAL_ACCOUNT_OF_BANK_B_LENGTH,
     *      maxMessage = "sync.transfer.personal_account_of_bank_b.range.max"
     * )
     */
    protected $personalAccountOfBankB;

    /**
     * [5]
     *
     * @ORM\Column(
     *      type = TransferRecord::DEBIT_CREDIT_PAYMENT_FLAG_TYPE
     * )
     *
     * @Assert\NotBlank(message = "sync.transfer.debit_credit_payment_flag.not_blank")
     * @Assert\Type(
     *     type    = "bool",
     *     message = "sync.transfer.debit_credit_payment_flag.type"
     * )
     */
    protected $debitCreditPaymentFlag;

    /**
     * [6]
     *
     * @ORM\Column(
     *      type   = TransferRecord::PAYMENT_AMOUNT_TYPE,
     *      length = TransferRecord::PAYMENT_AMOUNT_LENGTH
     * )
     *
     * @Assert\NotBlank(message="sync.transfer.payment_amount.not_blank")
     * @Assert\Range(
     *      max        = TransferRecord::PAYMENT_AMOUNT_LENGTH,
     *      maxMessage = "sync.transfer.payment_amount.range.max"
     * )
     */
    protected $paymentAmount;

    /**
     * [7]
     *
     * @ORM\Column(
     *      type   = TransferRecord::PAYMENT_DOCUMENT_TYPE_TYPE,
     *      length = TransferRecord::PAYMENT_DOCUMENT_TYPE_LENGTH
     * )
     *
     * @Assert\NotBlank(message="sync.transfer.payment_document_type.not_blank")
     * @Assert\Range(
     *      max        = TransferRecord::PAYMENT_DOCUMENT_TYPE_LENGTH,
     *      maxMessage = "sync.transfer.payment_document_type.range.max"
     * )
     */
    protected $paymentDocumentType;

    /**
     * [8]
     *
     * @ORM\Column(
     *      type   = TransferRecord::PAYMENT_OPERATIONAL_NUMBER_TYPE,
     *      length = TransferRecord::PAYMENT_OPERATIONAL_NUMBER_LENGTH
     * )
     *
     * @Assert\NotBlank(message="sync.transfer.payment_operational_number.not_blank")
     * @Assert\Length(
     *      max        = TransferRecord::PAYMENT_OPERATIONAL_NUMBER_LENGTH,
     *      maxMessage = "sync.transfer.payment_operational_number.length.max"
     * )
     */
    protected $paymentOperationalNumber;

    /**
     * [9]
     *
     * @ORM\Column(
     *      type   = TransferRecord::PAYMENT_CURRENCY_TYPE,
     *      length = TransferRecord::PAYMENT_CURRENCY_LENGTH
     * )
     *
     * @Assert\NotBlank(message="sync.transfer.payment_currency.not_blank")
     * @Assert\Range(
     *      max        = TransferRecord::PAYMENT_CURRENCY_LENGTH,
     *      maxMessage = "sync.transfer.payment_currency.range.max"
     * )
     */
    protected $paymentCurrency;

    /**
     * [10]
     *
     * @ORM\Column(type = TransferRecord::PAYMENT_DOCUMENT_DATE_TYPE)
     *
     * @Assert\NotBlank(message="sync.transfer.payment_document_date.not_blank")
     * @Assert\Date(message="sync.transfer.payment_document_date.date")
     */
    protected $paymentDocumentDate;

    /**
     * [11]
     *
     * @ORM\Column(type = TransferRecord::PAYMENT_DOCUMENT_ARRIVAL_DATE_TO_BANK_A_TYPE)
     *
     * @Assert\NotBlank(message="sync.transfer.payment_document_arrival_date_to_bank_a.not_blank")
     * @Assert\Date(message="sync.transfer.payment_document_date.date")
     */
    protected $paymentDocumentArrivalDateToBankA;

    /**
     * [12]
     *
     * @ORM\Column(
     *      type     = TransferRecord::PAYER_NAME_OF_CLIENT_A_TYPE,
     *      length   = TransferRecord::PAYER_NAME_OF_CLIENT_A_LENGTH,
     *      nullable = true
     * )
     *
     * @Assert\Length(
     *      max        = TransferRecord::PAYER_NAME_OF_CLIENT_A_LENGTH,
     *      maxMessage = "sync.transfer.payer_name_of_client_a.length.max"
     * )
     */
    protected $payerNameOfClientA;

    /**
     * [13]
     *
     * @ORM\Column(
     *      type     = TransferRecord::PAYER_NAME_OF_CLIENT_B_TYPE,
     *      length   = TransferRecord::PAYER_NAME_OF_CLIENT_B_LENGTH,
     *      nullable = true
     * )
     *
     * @Assert\Length(
     *      max        = TransferRecord::PAYER_NAME_OF_CLIENT_B_LENGTH,
     *      maxMessage = "sync.transfer.payer_name_of_client_a.length.max"
     * )
     */
    protected $payerNameOfClientB;

    /**
     * [14]
     *
     * @ORM\Column(
     *      type   = TransferRecord::PAYMENT_DESTINATION_TYPE,
     *      length = TransferRecord::PAYMENT_DESTINATION_LENGTH
     * )
     *
     * @Assert\NotBlank(message="sync.transfer.payment_destination.not_blank")
     * @Assert\Length(
     *      max        = TransferRecord::PAYMENT_DESTINATION_LENGTH,
     *      maxMessage = "sync.transfer.payment_destination.length.max"
     * )
     */
    protected $paymentDestination;

    /**
     * [15]
     *
     * @ORM\Column(
     *      type     = TransferRecord::SUPPORTING_PROPS_TYPE,
     *      length   = TransferRecord::SUPPORTING_PROPS_LENGTH,
     *      nullable = true
     * )
     *
     * @Assert\Length(
     *      max        = TransferRecord::SUPPORTING_PROPS_LENGTH,
     *      maxMessage = "sync.transfer.supporting_props.length.max"
     * )
     */
    protected $supportingProps;

    /**
     * [16]
     *
     * @ORM\Column(
     *      type     = TransferRecord::PAYMENT_DESTINATION_CODE_TYPE,
     *      length   = TransferRecord::PAYMENT_DESTINATION_CODE_LENGTH,
     *      nullable = true
     * )
     *
     * @Assert\Length(
     *      max        = TransferRecord::PAYMENT_DESTINATION_CODE_LENGTH,
     *      maxMessage = "sync.transfer.payment_destination_code.length.max"
     * )
     */
    protected $paymentDestinationCode;

    /**
     * [17]
     *
     * @ORM\Column(
     *      type     = TransferRecord::STRINGS_NUMBER_IN_BLOCK_TYPE,
     *      length   = TransferRecord::STRINGS_NUMBER_IN_BLOCK_LENGTH,
     *      nullable = true
     * )
     *
     * @Assert\Length(
     *      max        = TransferRecord::STRINGS_NUMBER_IN_BLOCK_LENGTH,
     *      maxMessage = "sync.transfer.strings_number_in_block.length.max"
     * )
     */
    protected $stringsNumberInBlock;

    /**
     * [18]
     *
     * @ORM\Column(
     *      type     = TransferRecord::CLIENT_IDENTIFIER_A_TYPE,
     *      length   = TransferRecord::CLIENT_IDENTIFIER_A_LENGTH,
     *      nullable = true
     * )
     *
     * @Assert\Length(
     *      max        = TransferRecord::CLIENT_IDENTIFIER_A_LENGTH,
     *      maxMessage = "sync.transfer.client_identifier_a.length.max"
     * )
     */
    protected $clientIdentifierA;

    /**
     * [19]
     *
     * @ORM\Column(
     *      type     = TransferRecord::CLIENT_IDENTIFIER_B_TYPE,
     *      length   = TransferRecord::CLIENT_IDENTIFIER_B_LENGTH,
     *      nullable = true
     * )
     *
     * @Assert\Length(
     *      max        = TransferRecord::CLIENT_IDENTIFIER_B_LENGTH,
     *      maxMessage = "sync.transfer.client_identifier_b.length.max"
     * )
     */
    protected $clientIdentifierB;

    /**
     * To string
     */
    public function __toString()
    {
        return (string)$this->transferId ?: 'TransferRecord';
    }

    /**
     * Get transferId
     *
     * @return guid
     */
    public function getTransferId()
    {
        return $this->transferId;
    }

    /**
     * Set mfoOffBankA
     *
     * @param integer $mfoOffBankA
     *
     * @return TransferRecord
     */
    public function setMfoOffBankA($mfoOffBankA)
    {
        $this->mfoOffBankA = $mfoOffBankA;

        return $this;
    }

    /**
     * Get mfoOffBankA
     *
     * @return integer
     */
    public function getMfoOffBankA()
    {
        return $this->mfoOffBankA;
    }

    /**
     * Set personalAccountOfBankA
     *
     * @param integer $personalAccountOfBankA
     *
     * @return TransferRecord
     */
    public function setPersonalAccountOfBankA($personalAccountOfBankA)
    {
        $this->personalAccountOfBankA = $personalAccountOfBankA;

        return $this;
    }

    /**
     * Get personalAccountOfBankA
     *
     * @return integer
     */
    public function getPersonalAccountOfBankA()
    {
        return $this->personalAccountOfBankA;
    }

    /**
     * Set mfoOfBankB
     *
     * @param integer $mfoOfBankB
     *
     * @return TransferRecord
     */
    public function setMfoOfBankB($mfoOfBankB)
    {
        $this->mfoOfBankB = $mfoOfBankB;

        return $this;
    }

    /**
     * Get mfoOfBankB
     *
     * @return integer
     */
    public function getMfoOfBankB()
    {
        return $this->mfoOfBankB;
    }

    /**
     * Set personalAccountOfBankB
     *
     * @param integer $personalAccountOfBankB
     *
     * @return TransferRecord
     */
    public function setPersonalAccountOfBankB($personalAccountOfBankB)
    {
        $this->personalAccountOfBankB = $personalAccountOfBankB;

        return $this;
    }

    /**
     * Get personalAccountOfBankB
     *
     * @return integer
     */
    public function getPersonalAccountOfBankB()
    {
        return $this->personalAccountOfBankB;
    }

    /**
     * Set debitCreditPaymentFlag
     *
     * @param boolean $debitCreditPaymentFlag
     *
     * @return TransferRecord
     */
    public function setDebitCreditPaymentFlag($debitCreditPaymentFlag)
    {
        $this->debitCreditPaymentFlag = $debitCreditPaymentFlag;

        return $this;
    }

    /**
     * Get debitCreditPaymentFlag
     *
     * @return boolean
     */
    public function getDebitCreditPaymentFlag()
    {
        return $this->debitCreditPaymentFlag;
    }

    /**
     * Set paymentAmount
     *
     * @param integer $paymentAmount
     *
     * @return TransferRecord
     */
    public function setPaymentAmount($paymentAmount)
    {
        $this->paymentAmount = $paymentAmount;

        return $this;
    }

    /**
     * Get paymentAmount
     *
     * @return integer
     */
    public function getPaymentAmount()
    {
        return $this->paymentAmount;
    }

    /**
     * Set paymentDocumentType
     *
     * @param integer $paymentDocumentType
     *
     * @return TransferRecord
     */
    public function setPaymentDocumentType($paymentDocumentType)
    {
        $this->paymentDocumentType = $paymentDocumentType;

        return $this;
    }

    /**
     * Get paymentDocumentType
     *
     * @return integer
     */
    public function getPaymentDocumentType()
    {
        return $this->paymentDocumentType;
    }

    /**
     * Set paymentOperationalNumber
     *
     * @param integer $paymentOperationalNumber
     *
     * @return TransferRecord
     */
    public function setPaymentOperationalNumber($paymentOperationalNumber)
    {
        $this->paymentOperationalNumber = $paymentOperationalNumber;

        return $this;
    }

    /**
     * Get paymentOperationalNumber
     *
     * @return integer
     */
    public function getPaymentOperationalNumber()
    {
        return $this->paymentOperationalNumber;
    }

    /**
     * Set paymentCurrency
     *
     * @param integer $paymentCurrency
     *
     * @return TransferRecord
     */
    public function setPaymentCurrency($paymentCurrency)
    {
        $this->paymentCurrency = $paymentCurrency;

        return $this;
    }

    /**
     * Get paymentCurrency
     *
     * @return integer
     */
    public function getPaymentCurrency()
    {
        return $this->paymentCurrency;
    }

    /**
     * Set paymentDocumentDate
     *
     * @param \DateTime $paymentDocumentDate
     *
     * @return TransferRecord
     */
    public function setPaymentDocumentDate($paymentDocumentDate)
    {
        $this->paymentDocumentDate = $paymentDocumentDate;

        return $this;
    }

    /**
     * Get paymentDocumentDate
     *
     * @return \DateTime
     */
    public function getPaymentDocumentDate()
    {
        return $this->paymentDocumentDate;
    }

    /**
     * Set paymentDocumentArrivalDateToBankA
     *
     * @param \DateTime $paymentDocumentArrivalDateToBankA
     *
     * @return TransferRecord
     */
    public function setPaymentDocumentArrivalDateToBankA($paymentDocumentArrivalDateToBankA)
    {
        $this->paymentDocumentArrivalDateToBankA = $paymentDocumentArrivalDateToBankA;

        return $this;
    }

    /**
     * Get paymentDocumentArrivalDateToBankA
     *
     * @return \DateTime
     */
    public function getPaymentDocumentArrivalDateToBankA()
    {
        return $this->paymentDocumentArrivalDateToBankA;
    }

    /**
     * Set payerNameOfClientA
     *
     * @param string $payerNameOfClientA
     *
     * @return TransferRecord
     */
    public function setPayerNameOfClientA($payerNameOfClientA)
    {
        $this->payerNameOfClientA = $payerNameOfClientA;

        return $this;
    }

    /**
     * Get payerNameOfClientA
     *
     * @return string
     */
    public function getPayerNameOfClientA()
    {
        return $this->payerNameOfClientA;
    }

    /**
     * Set payerNameOfClientB
     *
     * @param string $payerNameOfClientB
     *
     * @return TransferRecord
     */
    public function setPayerNameOfClientB($payerNameOfClientB)
    {
        $this->payerNameOfClientB = $payerNameOfClientB;

        return $this;
    }

    /**
     * Get payerNameOfClientB
     *
     * @return string
     */
    public function getPayerNameOfClientB()
    {
        return $this->payerNameOfClientB;
    }

    /**
     * Set paymentDestination
     *
     * @param string $paymentDestination
     *
     * @return TransferRecord
     */
    public function setPaymentDestination($paymentDestination)
    {
        $this->paymentDestination = $paymentDestination;

        return $this;
    }

    /**
     * Get paymentDestination
     *
     * @return string
     */
    public function getPaymentDestination()
    {
        return $this->paymentDestination;
    }

    /**
     * Set supportingProps
     *
     * @param string $supportingProps
     *
     * @return TransferRecord
     */
    public function setSupportingProps($supportingProps)
    {
        $this->supportingProps = $supportingProps;

        return $this;
    }

    /**
     * Get supportingProps
     *
     * @return string
     */
    public function getSupportingProps()
    {
        return $this->supportingProps;
    }

    /**
     * Set paymentDestinationCode
     *
     * @param string $paymentDestinationCode
     *
     * @return TransferRecord
     */
    public function setPaymentDestinationCode($paymentDestinationCode)
    {
        $this->paymentDestinationCode = $paymentDestinationCode;

        return $this;
    }

    /**
     * Get paymentDestinationCode
     *
     * @return string
     */
    public function getPaymentDestinationCode()
    {
        return $this->paymentDestinationCode;
    }

    /**
     * Set stringsNumberInBlock
     *
     * @param string $stringsNumberInBlock
     *
     * @return TransferRecord
     */
    public function setStringsNumberInBlock($stringsNumberInBlock)
    {
        $this->stringsNumberInBlock = $stringsNumberInBlock;

        return $this;
    }

    /**
     * Get stringsNumberInBlock
     *
     * @return string
     */
    public function getStringsNumberInBlock()
    {
        return $this->stringsNumberInBlock;
    }

    /**
     * Set clientIdentifierA
     *
     * @param string $clientIdentifierA
     *
     * @return TransferRecord
     */
    public function setClientIdentifierA($clientIdentifierA)
    {
        $this->clientIdentifierA = $clientIdentifierA;

        return $this;
    }

    /**
     * Get clientIdentifierA
     *
     * @return string
     */
    public function getClientIdentifierA()
    {
        return $this->clientIdentifierA;
    }

    /**
     * Set clientIdentifierB
     *
     * @param string $clientIdentifierB
     *
     * @return TransferRecord
     */
    public function setClientIdentifierB($clientIdentifierB)
    {
        $this->clientIdentifierB = $clientIdentifierB;

        return $this;
    }

    /**
     * Get clientIdentifierB
     *
     * @return string
     */
    public function getClientIdentifierB()
    {
        return $this->clientIdentifierB;
    }

    /**
     * Set transferFile
     *
     * @param \SyncBundle\Entity\BankingServer\Transfer\TransferFile $transferFile
     *
     * @return TransferRecord
     */
    public function setTransferFile(\SyncBundle\Entity\BankingServer\Transfer\TransferFile $transferFile = null)
    {
        $this->transferFile = $transferFile;

        return $this;
    }

    /**
     * Get transferFile
     *
     * @return \SyncBundle\Entity\BankingServer\Transfer\TransferFile
     */
    public function getTransferFile()
    {
        return $this->transferFile;
    }
}
