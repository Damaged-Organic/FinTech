<?php
// src/AppBundle/Entity/Account/AccountGroup.php
namespace AppBundle\Entity\Account;

use Symfony\Component\Validator\Constraints as Assert,
    Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait,
    AppBundle\Entity\Utility\Traits\DoctrineMapping\PseudoDeleteMapperTrait;

/**
 * @ORM\Table(name="accounts_groups")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Account\Repository\AccountGroupRepository")
 *
 * @UniqueEntity(fields="name", message="account_group.name.unique")
 */
class AccountGroup
{
    use IdMapperTrait, PseudoDeleteMapperTrait;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Organization\Organization", inversedBy="accountGroups")
     * @ORM\JoinColumn(name="organization_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $organization;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Account\Account", mappedBy="accountGroup")
     */
    protected $accounts;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\BankingMachine\BankingMachine", mappedBy="accountGroups")
     */
    protected $bankingMachines;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Operator\Operator", mappedBy="accountGroups")
     */
    protected $operators;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Transaction\Transaction", mappedBy="accountGroup")
     */
    protected $transactions;

    /**
     * @ORM\Column(type="string", length=250, unique=true)
     *
     * @Assert\NotBlank(message="account_group.name.not_blank")
     * @Assert\Length(
     *      min=2,
     *      max=250,
     *      minMessage="account_group.name.length.min",
     *      maxMessage="account_group.name.length.max"
     * )
     */
    protected $name;

    public function __construct()
    {
        $this->accounts        = new ArrayCollection;
        $this->bankingMachines = new ArrayCollection;
        $this->operators       = new ArrayCollection;
    }

    public function __toString()
    {
        return ( $this->name ) ? $this->name : "";
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return AccountGroup
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
     * Set organization
     *
     * @param \AppBundle\Entity\Organization\Organization $organization
     *
     * @return AccountGroup
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

    /**
     * Add account
     *
     * @param \AppBundle\Entity\Account\Account $account
     *
     * @return AccountGroup
     */
    public function addAccount(\AppBundle\Entity\Account\Account $account)
    {
        $account->setAccountGroup($this);
        $this->accounts[] = $account;

        return $this;
    }

    /**
     * Remove account
     *
     * @param \AppBundle\Entity\Account\Account $account
     */
    public function removeAccount(\AppBundle\Entity\Account\Account $account)
    {
        $this->accounts->removeElement($account);
    }

    /**
     * Get accounts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAccounts()
    {
        return $this->accounts;
    }

    /**
     * Add bankingMachine
     *
     * @param \AppBundle\Entity\BankingMachine\BankingMachine $bankingMachine
     *
     * @return AccountGroup
     */
    public function addBankingMachine(\AppBundle\Entity\BankingMachine\BankingMachine $bankingMachine)
    {
        $this->bankingMachines[] = $bankingMachine;

        return $this;
    }

    /**
     * Remove bankingMachine
     *
     * @param \AppBundle\Entity\BankingMachine\BankingMachine $bankingMachine
     */
    public function removeBankingMachine(\AppBundle\Entity\BankingMachine\BankingMachine $bankingMachine)
    {
        $this->bankingMachines->removeElement($bankingMachine);
    }

    /**
     * Get bankingMachines
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBankingMachines()
    {
        return $this->bankingMachines;
    }

    /**
     * Add operator
     *
     * @param \AppBundle\Entity\Operator\Operator $operator
     *
     * @return AccountGroup
     */
    public function addOperator(\AppBundle\Entity\Operator\Operator $operator)
    {
        $this->operators[] = $operator;

        return $this;
    }

    /**
     * Remove operator
     *
     * @param \AppBundle\Entity\Operator\Operator $operator
     */
    public function removeOperator(\AppBundle\Entity\Operator\Operator $operator)
    {
        $this->operators->removeElement($operator);
    }

    /**
     * Get operators
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOperators()
    {
        return $this->operators;
    }

    /**
     * Add transaction
     *
     * @param \AppBundle\Entity\Transaction\Transaction $transaction
     *
     * @return AccountGroup
     */
    public function addTransaction(\AppBundle\Entity\Transaction\Transaction $transaction)
    {
        $transaction->setAccountGroup($this);
        $this->transactions[] = $transaction;

        return $this;
    }

    /**
     * Remove transaction
     *
     * @param \AppBundle\Entity\Transaction\Transaction $transaction
     */
    public function removeTransaction(\AppBundle\Entity\Transaction\Transaction $transaction)
    {
        $this->transactions->removeElement($transaction);
    }

    /**
     * Get transactions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTransactions()
    {
        return $this->transactions;
    }
}
