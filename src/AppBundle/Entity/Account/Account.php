<?php
// src/AppBundle/Entity/Account/Account.php
namespace AppBundle\Entity\Account;

use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait,
    AppBundle\Entity\Utility\Traits\DoctrineMapping\PseudoDeleteMapperTrait,
    AppBundle\Entity\Account\Properties\AccountPropertiesInterface,
    AppBundle\Entity\Account\Utility\Interfaces\AccountAttributesInterface;

/**
 * @ORM\Table(name="accounts")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Account\Repository\AccountRepository")
 */
class Account implements AccountPropertiesInterface, AccountAttributesInterface
{
    use IdMapperTrait, PseudoDeleteMapperTrait;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Account\AccountGroup", inversedBy="accounts")
     * @ORM\JoinColumn(name="account_group_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $accountGroup;

    /**
     * @ORM\Column(type="string", length=250)
     *
     * @Assert\NotBlank(message="account.name.not_blank")
     * @Assert\Length(
     *      min=2,
     *      max=250,
     *      minMessage="account.name.length.min",
     *      maxMessage="account.name.length.max"
     * )
     */
    protected $name;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     *
     * @Assert\NotBlank(message="account.percent.not_blank")
     * @Assert\Range(
     *      min=0,
     *      max=100,
     *      minMessage="account.percent.range.min",
     *      maxMessage="account.percent.range.max"
     * )
     */
    protected $percent;

    /**
     * @ORM\Column(
     *      type   = Account::MFO_OF_BANK_A_TYPE,
     *      length = Account::MFO_OF_BANK_A_LENGTH
     * )
     *
     * @Assert\NotBlank(message = "account.mfo_of_bank_a.not_blank")
     * @Assert\Length(
     *      max        = Account::MFO_OF_BANK_A_LENGTH,
     *      maxMessage = "account.mfo_of_bank_a.range.max"
     * )
     */
    protected $mfoOfBankA;

    /**
     * @ORM\Column(
     *      type   = Account::PERSONAL_ACCOUNT_OF_BANK_A_TYPE,
     *      length = Account::PERSONAL_ACCOUNT_OF_BANK_A_LENGTH
     * )
     *
     * @Assert\NotBlank(message = "account.personal_account_of_bank_a.not_blank")
     * @Assert\Length(
     *      max        = Account::PERSONAL_ACCOUNT_OF_BANK_A_LENGTH,
     *      maxMessage = "account.personal_account_of_bank_a.range.max"
     * )
     */
    protected $personalAccountOfBankA;

    /**
     * @ORM\Column(
     *      type   = Account::MFO_OF_BANK_B_TYPE,
     *      length = Account::MFO_OF_BANK_B_LENGTH
     * )
     *
     * @Assert\NotBlank(message = "account.mfo_of_bank_b.not_blank")
     * @Assert\Length(
     *      max        = Account::MFO_OF_BANK_B_LENGTH,
     *      maxMessage = "account.mfo_of_bank_b.range.max"
     * )
     */
    protected $mfoOfBankB;

    /**
     * @ORM\Column(
     *      type   = Account::PERSONAL_ACCOUNT_OF_BANK_B_TYPE,
     *      length = Account::PERSONAL_ACCOUNT_OF_BANK_B_LENGTH
     * )
     *
     * @Assert\NotBlank(message = "account.personal_account_of_bank_b.not_blank")
     * @Assert\Length(
     *      max        = Account::PERSONAL_ACCOUNT_OF_BANK_B_LENGTH,
     *      maxMessage = "account.personal_account_of_bank_b.range.max"
     * )
     */
    protected $personalAccountOfBankB;

    /**
     * @ORM\Column(
     *      type = Account::DEBIT_CREDIT_PAYMENT_FLAG_TYPE
     * )
     *
     * @Assert\Type(
     *     type    = "bool",
     *     message = "account.debit_credit_payment_flag.type"
     * )
     */
    protected $debitCreditPaymentFlag;

    /**
     * @ORM\Column(
     *      type   = Account::PAYMENT_AMOUNT_TYPE,
     *      length = Account::PAYMENT_AMOUNT_LENGTH
     * )
     *
     * @Assert\NotBlank(message="account.payment_amount.not_blank")
     * @Assert\Length(
     *      max        = Account::PAYMENT_AMOUNT_LENGTH,
     *      maxMessage = "account.payment_amount.range.max"
     * )
     */
    protected $paymentAmount;

    /**
     * @ORM\Column(
     *      type   = Account::PAYMENT_DOCUMENT_TYPE_TYPE,
     *      length = Account::PAYMENT_DOCUMENT_TYPE_LENGTH
     * )
     *
     * @Assert\NotBlank(message="account.payment_document_type.not_blank")
     * @Assert\Length(
     *      max        = Account::PAYMENT_DOCUMENT_TYPE_LENGTH,
     *      maxMessage = "account.payment_document_type.range.max"
     * )
     */
    protected $paymentDocumentType;

    /**
     * @ORM\Column(
     *      type   = Account::PAYMENT_OPERATIONAL_NUMBER_TYPE,
     *      length = Account::PAYMENT_OPERATIONAL_NUMBER_LENGTH
     * )
     *
     * @Assert\NotBlank(message="account.payment_operational_number.not_blank")
     * @Assert\Length(
     *      max        = Account::PAYMENT_OPERATIONAL_NUMBER_LENGTH,
     *      maxMessage = "account.payment_operational_number.length.max"
     * )
     */
    protected $paymentOperationalNumber;

    /**
     * @ORM\Column(
     *      type   = Account::PAYMENT_CURRENCY_TYPE,
     *      length = Account::PAYMENT_CURRENCY_LENGTH
     * )
     *
     * @Assert\NotBlank(message="account.payment_currency.not_blank")
     * @Assert\Length(
     *      max        = Account::PAYMENT_CURRENCY_LENGTH,
     *      maxMessage = "account.payment_currency.range.max"
     * )
     */
    protected $paymentCurrency;

    /**
     * @ORM\Column(type = Account::PAYMENT_DOCUMENT_DATE_TYPE)
     *
     * @Assert\NotBlank(message="account.payment_document_date.not_blank")
     * @Assert\Date(message="account.payment_document_date.date")
     */
    protected $paymentDocumentDate;

    /**
     * @ORM\Column(type = Account::PAYMENT_DOCUMENT_ARRIVAL_DATE_TO_BANK_A_TYPE)
     *
     * @Assert\NotBlank(message="account.payment_document_arrival_date_to_bank_a.not_blank")
     * @Assert\Date(message="account.payment_document_date.date")
     */
    protected $paymentDocumentArrivalDateToBankA;

    /**
     * @ORM\Column(
     *      type     = Account::PAYER_NAME_OF_CLIENT_A_TYPE,
     *      length   = Account::PAYER_NAME_OF_CLIENT_A_LENGTH,
     *      nullable = true
     * )
     *
     * @Assert\Length(
     *      max        = Account::PAYER_NAME_OF_CLIENT_A_LENGTH,
     *      maxMessage = "account.payer_name_of_client_a.length.max"
     * )
     */
    protected $payerNameOfClientA;

    /**
     * @ORM\Column(
     *      type     = Account::PAYER_NAME_OF_CLIENT_B_TYPE,
     *      length   = Account::PAYER_NAME_OF_CLIENT_B_LENGTH,
     *      nullable = true
     * )
     *
     * @Assert\Length(
     *      max        = Account::PAYER_NAME_OF_CLIENT_B_LENGTH,
     *      maxMessage = "account.payer_name_of_client_a.length.max"
     * )
     */
    protected $payerNameOfClientB;

    /**
     * @ORM\Column(
     *      type   = Account::PAYMENT_DESTINATION_TYPE,
     *      length = Account::PAYMENT_DESTINATION_LENGTH
     * )
     *
     * @Assert\NotBlank(message="account.payment_destination.not_blank")
     * @Assert\Length(
     *      max        = Account::PAYMENT_DESTINATION_LENGTH,
     *      maxMessage = "account.payment_destination.length.max"
     * )
     */
    protected $paymentDestination;

    /**
     * @ORM\Column(
     *      type     = Account::SUPPORTING_PROPS_TYPE,
     *      length   = Account::SUPPORTING_PROPS_LENGTH,
     *      nullable = true
     * )
     *
     * @Assert\Length(
     *      max        = Account::SUPPORTING_PROPS_LENGTH,
     *      maxMessage = "account.supporting_props.length.max"
     * )
     */
    protected $supportingProps;

    /**
     * @ORM\Column(
     *      type     = Account::PAYMENT_DESTINATION_CODE_TYPE,
     *      length   = Account::PAYMENT_DESTINATION_CODE_LENGTH,
     *      nullable = true
     * )
     *
     * @Assert\Length(
     *      max        = Account::PAYMENT_DESTINATION_CODE_LENGTH,
     *      maxMessage = "account.payment_destination_code.length.max"
     * )
     */
    protected $paymentDestinationCode;

    /**
     * @ORM\Column(
     *      type     = Account::STRINGS_NUMBER_IN_BLOCK_TYPE,
     *      length   = Account::STRINGS_NUMBER_IN_BLOCK_LENGTH,
     *      nullable = true
     * )
     *
     * @Assert\Length(
     *      max        = Account::STRINGS_NUMBER_IN_BLOCK_LENGTH,
     *      maxMessage = "account.strings_number_in_block.length.max"
     * )
     */
    protected $stringsNumberInBlock;

    /**
     * @ORM\Column(
     *      type     = Account::CLIENT_IDENTIFIER_A_TYPE,
     *      length   = Account::CLIENT_IDENTIFIER_A_LENGTH,
     *      nullable = true
     * )
     *
     * @Assert\Length(
     *      max        = Account::CLIENT_IDENTIFIER_A_LENGTH,
     *      maxMessage = "account.client_identifier_a.length.max"
     * )
     */
    protected $clientIdentifierA;

    /**
     * @ORM\Column(
     *      type     = Account::CLIENT_IDENTIFIER_B_TYPE,
     *      length   = Account::CLIENT_IDENTIFIER_B_LENGTH,
     *      nullable = true
     * )
     *
     * @Assert\Length(
     *      max        = Account::CLIENT_IDENTIFIER_B_LENGTH,
     *      maxMessage = "account.client_identifier_b.length.max"
     * )
     */
    protected $clientIdentifierB;

    public function __toString()
    {
        return ( $this->id ) ? $this->id : "";
    }

    /**
     * Set accountGroup
     *
     * @param \AppBundle\Entity\Account\AccountGroup $accountGroup
     *
     * @return Account
     */
    public function setAccountGroup(\AppBundle\Entity\Account\AccountGroup $accountGroup = null)
    {
        $this->accountGroup = $accountGroup;

        return $this;
    }

    /**
     * Get accountGroup
     *
     * @return \AppBundle\Entity\Account\AccountGroup
     */
    public function getAccountGroup()
    {
        return $this->accountGroup;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Account
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set percent
     *
     * @param integer $percent
     *
     * @return Account
     */
    public function setPercent($percent)
    {
        $this->percent = $percent;

        return $this;
    }

    /**
     * Get percent
     *
     * @return integer
     */
    public function getPercent()
    {
        return $this->percent;
    }

    /**
     * Set mfoOfBankA
     *
     * @param integer $mfoOfBankA
     *
     * @return Account
     */
    public function setMfoOfBankA($mfoOfBankA)
    {
        $this->mfoOfBankA = $mfoOfBankA;

        return $this;
    }

    /**
     * Get mfoOfBankA
     *
     * @return integer
     */
    public function getMfoOfBankA()
    {
        return $this->mfoOfBankA;
    }

    /**
     * Set personalAccountOfBankA
     *
     * @param integer $personalAccountOfBankA
     *
     * @return Account
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
     * @return Account
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
     * @return Account
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
     * @return Account
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
     * @return Account
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
     * @return Account
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
     * @return Account
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
     * @return Account
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
     * @return Account
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
     * @return Account
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
     * @return Account
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
     * @return Account
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
     * @return Account
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
     * @return Account
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
     * @return Account
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
     * @return Account
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
     * @return Account
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
     * @return Account
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

    /*-------------------------------------------------------------------------
    | INTERFACE IMPLEMENTATION
    |------------------------------------------------------------------------*/

    static public function getProperties()
    {
        return [
            self::PROPERTY_ID,
            self::PROPERTY_NAME,
        ];
    }
}
