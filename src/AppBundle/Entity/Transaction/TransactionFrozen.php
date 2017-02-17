<?php
// src/AppBundle/Entity/Transaction/TransactionFrozen.php
namespace AppBundle\Entity\Transaction;

use Doctrine\ORM\Mapping as ORM;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait,
    AppBundle\Entity\Transaction\Transaction;

/**
 * @ORM\Table(name="transactions_frozens")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Transaction\Repository\TransactionFrozenRepository")
 */
class TransactionFrozen
{
    use IdMapperTrait;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Transaction\Transaction", inversedBy="transactionFrozen")
     * @ORM\JoinColumn(name="transaction_id", referencedColumnName="id")
     */
    protected $transaction;

    /**
     * @ORM\Column(type="bigint")
     */
    protected $organizationId;

    /**
     * @ORM\Column(type="string", length=250)
     */
    protected $organizationName;

    /**
     * @ORM\Column(type="bigint")
     */
    protected $bankingMachineId;

    /**
     * @ORM\Column(type="string", length=16)
     */
    protected $bankingMachineSerial;

    /**
     * @ORM\Column(type="string", length=500)
     */
    protected $bankingMachineAddress;

    /**
     * @ORM\Column(type="bigint")
     */
    protected $operatorId;

    /**
     * @ORM\Column(type="string", length=300)
     */
    protected $operatorFullName;

    /**
     * @ORM\Column(type="bigint")
     */
    protected $nfcTagId;

    /**
     * @ORM\Column(type="string", length=8)
     */
    protected $nfcTagNumber;

    /**
     * @ORM\Column(type="string", length=32)
     */
    protected $nfcTagCode;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     */
    protected $accountGroupId;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    protected $accountGroupName;

    public function __toString()
    {
        return (string)$this->id ?: static::class;
    }

    /**
     * Set organizationId
     *
     * @param integer $organizationId
     *
     * @return TransactionFrozen
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;

        return $this;
    }

    /**
     * Get organizationId
     *
     * @return integer
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     * Set organizationName
     *
     * @param string $organizationName
     *
     * @return TransactionFrozen
     */
    public function setOrganizationName($organizationName)
    {
        $this->organizationName = $organizationName;

        return $this;
    }

    /**
     * Get organizationName
     *
     * @return string
     */
    public function getOrganizationName()
    {
        return $this->organizationName;
    }

    /**
     * Set bankingMachineId
     *
     * @param integer $bankingMachineId
     *
     * @return TransactionFrozen
     */
    public function setBankingMachineId($bankingMachineId)
    {
        $this->bankingMachineId = $bankingMachineId;

        return $this;
    }

    /**
     * Get bankingMachineId
     *
     * @return integer
     */
    public function getBankingMachineId()
    {
        return $this->bankingMachineId;
    }

    /**
     * Set bankingMachineSerial
     *
     * @param string $bankingMachineSerial
     *
     * @return TransactionFrozen
     */
    public function setBankingMachineSerial($bankingMachineSerial)
    {
        $this->bankingMachineSerial = $bankingMachineSerial;

        return $this;
    }

    /**
     * Get bankingMachineSerial
     *
     * @return string
     */
    public function getBankingMachineSerial()
    {
        return $this->bankingMachineSerial;
    }

    /**
     * Set bankingMachineAddress
     *
     * @param string $bankingMachineAddress
     *
     * @return TransactionFrozen
     */
    public function setBankingMachineAddress($bankingMachineAddress)
    {
        $this->bankingMachineAddress = $bankingMachineAddress;

        return $this;
    }

    /**
     * Get bankingMachineAddress
     *
     * @return string
     */
    public function getBankingMachineAddress()
    {
        return $this->bankingMachineAddress;
    }

    /**
     * Set operatorId
     *
     * @param integer $operatorId
     *
     * @return TransactionFrozen
     */
    public function setOperatorId($operatorId)
    {
        $this->operatorId = $operatorId;

        return $this;
    }

    /**
     * Get operatorId
     *
     * @return integer
     */
    public function getOperatorId()
    {
        return $this->operatorId;
    }

    /**
     * Set operatorFullName
     *
     * @param string $operatorFullName
     *
     * @return TransactionFrozen
     */
    public function setOperatorFullName($operatorFullName)
    {
        $this->operatorFullName = $operatorFullName;

        return $this;
    }

    /**
     * Get operatorFullName
     *
     * @return string
     */
    public function getOperatorFullName()
    {
        return $this->operatorFullName;
    }

    /**
     * Set nfcTagId
     *
     * @param integer $nfcTagId
     *
     * @return TransactionFrozen
     */
    public function setNfcTagId($nfcTagId)
    {
        $this->nfcTagId = $nfcTagId;

        return $this;
    }

    /**
     * Get nfcTagId
     *
     * @return integer
     */
    public function getNfcTagId()
    {
        return $this->nfcTagId;
    }

    /**
     * Set nfcTagNumber
     *
     * @param string $nfcTagNumber
     *
     * @return TransactionFrozen
     */
    public function setNfcTagNumber($nfcTagNumber)
    {
        $this->nfcTagNumber = $nfcTagNumber;

        return $this;
    }

    /**
     * Get nfcTagNumber
     *
     * @return string
     */
    public function getNfcTagNumber()
    {
        return $this->nfcTagNumber;
    }

    /**
     * Set nfcTagCode
     *
     * @param string $nfcTagCode
     *
     * @return TransactionFrozen
     */
    public function setNfcTagCode($nfcTagCode)
    {
        $this->nfcTagCode = $nfcTagCode;

        return $this;
    }

    /**
     * Get nfcTagCode
     *
     * @return string
     */
    public function getNfcTagCode()
    {
        return $this->nfcTagCode;
    }

    /**
     * Set accountGroupId
     *
     * @param integer $accountGroupId
     *
     * @return TransactionFrozen
     */
    public function setAccountGroupId($accountGroupId)
    {
        $this->accountGroupId = $accountGroupId;

        return $this;
    }

    /**
     * Get accountGroupId
     *
     * @return integer
     */
    public function getAccountGroupId()
    {
        return $this->accountGroupId;
    }

    /**
     * Set accountGroupName
     *
     * @param string $accountGroupName
     *
     * @return TransactionFrozen
     */
    public function setAccountGroupName($accountGroupName)
    {
        $this->accountGroupName = $accountGroupName;

        return $this;
    }

    /**
     * Get accountGroupName
     *
     * @return string
     */
    public function getAccountGroupName()
    {
        return $this->accountGroupName;
    }

    /**
     * Set transaction
     *
     * @param \AppBundle\Entity\Transaction\Transaction $transaction
     *
     * @return TransactionFrozen
     */
    public function setTransaction(\AppBundle\Entity\Transaction\Transaction $transaction = null)
    {
        $this->transaction = $transaction;

        return $this;
    }

    /**
     * Get transaction
     *
     * @return \AppBundle\Entity\Transaction\Transaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /*-------------------------------------------------------------------------
    | CUSTOM METHODS
    |------------------------------------------------------------------------*/

    public function freeze(Transaction $transaction)
    {
        $this->setTransaction($transaction);

        # Organization
        if( $organization = $transaction->getOrganization() ) {
            $this
                ->setOrganizationId($organization->getId())
                ->setOrganizationName($organization->getName())
            ;
        }

        # Banking Machine
        if( $bankingMachine = $transaction->getBankingMachine() ) {
            $this
                ->setBankingMachineId($bankingMachine->getId())
                ->setBankingMachineSerial($bankingMachine->getSerial())
                ->setBankingMachineAddress($bankingMachine->getAddress())
            ;
        }

        # Operator
        if( $operator = $transaction->getOperator() ) {
            $this
                ->setOperatorId($operator->getId())
                ->setOperatorFullName($operator->getFullName())
            ;

            # NFC Tag
            if( $nfcTag = $operator->getNfcTag() ) {
                $this
                    ->setNfcTagId($nfcTag->getId())
                    ->setNfcTagNumber($nfcTag->getNumber())
                    ->setNfcTagCode($nfcTag->getCode())
                ;
            }
        }

        # Account Group
        if( $accountGroup = $transaction->getAccountGroup() ) {
            $this
                ->setAccountGroupId($accountGroup->getId())
                ->setAccountGroupName($accountGroup->getName())
            ;
        }

        return $this;
    }
}
