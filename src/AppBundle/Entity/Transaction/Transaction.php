<?php
// src/AppBundle/Entity/Transaction/Transaction.php
namespace AppBundle\Entity\Transaction;

use Symfony\Component\Validator\Constraints as Assert,
    Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait,
    AppBundle\Entity\Tansaction\TansactionFrozen,
    AppBundle\Entity\Transaction\Properties\TransactionPropertiesInterface;

/**
 * @ORM\Table(name="transactions")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Transaction\Repository\TransactionRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator", type="string")
 * @ORM\DiscriminatorMap({"replenishment" = "Replenishment", "collection" = "Collection"})
 *
 * @UniqueEntity(fields="transactionId", message="transaction.transaction_id.unique")
 */
abstract class Transaction implements TransactionPropertiesInterface
{
    use IdMapperTrait;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Transaction\TransactionFrozen", mappedBy="transaction")
     */
    protected $transactionFrozen;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Organization\Organization", inversedBy="transactions")
     * @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     */
    protected $organization;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BankingMachine\BankingMachine", inversedBy="transactions")
     * @ORM\JoinColumn(name="banking_machine_id", referencedColumnName="id")
     */
    protected $bankingMachine;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Operator\Operator", inversedBy="transactions")
     * @ORM\JoinColumn(name="operator_id", referencedColumnName="id")
     */
    protected $operator;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Account\AccountGroup", inversedBy="transactions")
     * @ORM\JoinColumn(name="account_group_id", referencedColumnName="id")
     */
    protected $accountGroup;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Banknote\BanknoteList", mappedBy="transaction")
     */
    protected $banknoteLists;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    protected $syncId;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $syncAt;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $transactionAt;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $transactionFunds;

    public function __construct()
    {
        $this->banknoteLists = new ArrayCollection;
    }

    public function __toString()
    {
        return (string)$this->id ?: static::class;
    }

    static public function getTypeIdString()
    {
        return 'transaction_read';
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return Transaction
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set syncId
     *
     * @param string $syncId
     *
     * @return Transaction
     */
    public function setSyncId($syncId)
    {
        $this->syncId = $syncId;

        return $this;
    }

    /**
     * Get syncId
     *
     * @return string
     */
    public function getSyncId()
    {
        return $this->syncId;
    }

    /**
     * Set syncAt
     *
     * @param \DateTime $syncAt
     *
     * @return Transaction
     */
    public function setSyncAt($syncAt)
    {
        $this->syncAt = $syncAt;

        return $this;
    }

    /**
     * Get syncAt
     *
     * @return \DateTime
     */
    public function getSyncAt()
    {
        return $this->syncAt;
    }

    /**
     * Set transactionAt
     *
     * @param \DateTime $transactionAt
     *
     * @return Transaction
     */
    public function setTransactionAt($transactionAt)
    {
        $this->transactionAt = $transactionAt;

        return $this;
    }

    /**
     * Get transactionAt
     *
     * @return \DateTime
     */
    public function getTransactionAt()
    {
        return $this->transactionAt;
    }

    /**
     * Set transactionFrozen
     *
     * @param \AppBundle\Entity\Transaction\TransactionFrozen $transactionFrozen
     *
     * @return Transaction
     */
    public function setTransactionFrozen(\AppBundle\Entity\Transaction\TransactionFrozen $transactionFrozen = null)
    {
        $this->transactionFrozen = $transactionFrozen;

        return $this;
    }

    /**
     * Get transactionFrozen
     *
     * @return \AppBundle\Entity\Transaction\TransactionFrozen
     */
    public function getTransactionFrozen()
    {
        return $this->transactionFrozen;
    }

    /**
     * Set bankingMachine
     *
     * @param \AppBundle\Entity\BankingMachine\BankingMachine $bankingMachine
     *
     * @return Transaction
     */
    public function setBankingMachine(\AppBundle\Entity\BankingMachine\BankingMachine $bankingMachine = null)
    {
        $this->bankingMachine = $bankingMachine;

        return $this;
    }

    /**
     * Get bankingMachine
     *
     * @return \AppBundle\Entity\BankingMachine\BankingMachine
     */
    public function getBankingMachine()
    {
        return $this->bankingMachine;
    }

    /**
     * Set operator
     *
     * @param \AppBundle\Entity\Operator\Operator $operator
     *
     * @return Transaction
     */
    public function setOperator(\AppBundle\Entity\Operator\Operator $operator = null)
    {
        $this->operator = $operator;

        return $this;
    }

    /**
     * Get operator
     *
     * @return \AppBundle\Entity\Operator\Operator
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * Set accountGroup
     *
     * @param \AppBundle\Entity\Account\AccountGroup $accountGroup
     *
     * @return Transaction
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
     * Add banknoteList
     *
     * @param \AppBundle\Entity\Banknote\BanknoteList $banknoteList
     *
     * @return Transaction
     */
    public function addBanknoteList(\AppBundle\Entity\Banknote\BanknoteList $banknoteList)
    {
        $banknoteList->setTransaction($this);
        $this->banknoteLists[] = $banknoteList;

        // IMPORTANT: Inner recalculation of total amount of transaction funds
        $this->setTransactionFunds();

        return $this;
    }

    /**
     * Remove banknoteList
     *
     * @param \AppBundle\Entity\Banknote\BanknoteList $banknoteList
     */
    public function removeBanknoteList(\AppBundle\Entity\Banknote\BanknoteList $banknoteList)
    {
        $this->banknoteLists->removeElement($banknoteList);

        // IMPORTANT: Inner recalculation of total amount of transaction funds
        $this->setTransactionFunds();
    }

    /**
     * Get banknoteLists
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBanknoteLists()
    {
        return $this->banknoteLists;
    }

    /**
     * Set organization
     *
     * @param \AppBundle\Entity\Organization\Organization $organization
     *
     * @return Transaction
     */
    public function setOrganization(\AppBundle\Entity\Organization\Organization $organization = null)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * Get organization
     *
     * @return \AppBundle\Entity\Organization\Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /*-------------------------------------------------------------------------
    | CUSTOM SETTERS\GETTERS
    |------------------------------------------------------------------------*/

    private function getTransactionFundsGenerator($banknoteLists)
    {
        foreach($banknoteLists as $banknoteList)
        {
            if( $banknoteList->getQuantity() && $banknoteList->getBanknote() )
            {
                yield bcmul(
                    $banknoteList->getQuantity(), $banknoteList->getBanknote()->getNominal(), 2
                );
            }
        }
    }

    public function setTransactionFunds()
    {
        if( !$this->getBanknoteLists() )
            return FALSE;

        $totalFunds = 0;
        foreach( $this->getTransactionFundsGenerator($this->getBanknoteLists()) as $value )
        {
            $totalFunds = bcadd($totalFunds, $value, 2);
        }

        $this->transactionFunds = $totalFunds;

        return $this;
    }

    public function getTransactionFunds()
    {
        return $this->transactionFunds;
    }

    /*-------------------------------------------------------------------------
    | CUSTOM METHODS
    |------------------------------------------------------------------------*/

    public function freeze()
    {
        return (new TransactionFrozen)->freeze($this);
    }

    /*-------------------------------------------------------------------------
    | INTERFACE IMPLEMENTATION
    |------------------------------------------------------------------------*/

    static public function getProperties()
    {
        return [
            self::PROPERTY_TRANSACTION_AT,
        ];
    }
}
