<?php
// src/AppBundle/Entity/BankingMachine/BankingMachine.php
namespace AppBundle\Entity\BankingMachine;

use Symfony\Component\Validator\Constraints as Assert,
    Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait,
    AppBundle\Entity\Utility\Traits\DoctrineMapping\PseudoDeleteMapperTrait,
    AppBundle\Entity\Utility\Traits\Security\ApiTokenTrait,
    AppBundle\Entity\BankingMachine\Properties\BankingMachinePropertiesInterface,
    AppBundle\Validator\Constraints as CustomAssert;

/**
 * @ORM\Table(name="banking_machines")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\BankingMachine\Repository\BankingMachineRepository")
 *
 * @UniqueEntity(fields="serial", message="banking_machine.serial.unique")
 * @UniqueEntity(fields="login", message="banking_machine.login.unique")
 * @UniqueEntity(fields="name", message="banking_machine.name.unique")
 */
class BankingMachine implements BankingMachinePropertiesInterface
{
    use IdMapperTrait, PseudoDeleteMapperTrait, ApiTokenTrait;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Organization\Organization", inversedBy="bankingMachines")
     * @ORM\JoinColumn(name="organization_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $organization;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\BankingMachine\BankingMachineSync", mappedBy="bankingMachine")
     * @ORM\OrderBy({"syncedAt"="DESC"})
     */
    protected $bankingMachineSyncs;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\BankingMachine\BankingMachineEvent", mappedBy="bankingMachine")
     * @ORM\OrderBy({"occurredAt"="DESC"})
     */
    protected $bankingMachineEvents;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Operator\Operator", inversedBy="bankingMachines", indexBy="id")
     * @ORM\JoinTable(name="banking_machines_operators")
     */
    protected $operators;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Account\AccountGroup", inversedBy="bankingMachines", indexBy="id")
     * @ORM\JoinTable(name="banking_machines_accounts_groups")
     */
    protected $accountGroups;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Transaction\Transaction", mappedBy="bankingMachine")
     */
    protected $transactions;

    /**
     * @ORM\Column(type="string", length=16, unique=true)
     *
     * @Assert\NotBlank(message="banking_machine.serial.not_blank")
     * @Assert\Length(
     *      min=1,
     *      max=16,
     *      minMessage="banking_machine.serial.length.min",
     *      maxMessage="banking_machine.serial.length.max"
     * )
     */
    protected $serial;

    /**
     * @ORM\Column(type="string", length=64, nullable=true, unique=true)
     * @Assert\Length(
     *      min=4,
     *      max=64,
     *      minMessage="banking_machine.login.length.min",
     *      maxMessage="banking_machine.login.length.max"
     * )
     */
    protected $login;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Assert\Length(
     *      min=8,
     *      max=64,
     *      minMessage="banking_machine.password.length.min",
     *      maxMessage="banking_machine.password.length.max"
     * )
     */
    protected $password;

    /**
     * @ORM\Column(type="string", length=250, unique=true)
     *
     * @Assert\NotBlank(message="banking_machine.name.not_blank")
     * @Assert\Length(
     *      min=4,
     *      max=64,
     *      minMessage="banking_machine.name.length.min",
     *      maxMessage="banking_machine.name.length.max"
     * )
     * @CustomAssert\IsDeviceName
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=500)
     *
     * @Assert\NotBlank(message="banking_machine.address.not_blank")
     * @Assert\Length(
     *      min=2,
     *      max=500,
     *      minMessage="banking_machine.address.length.min",
     *      maxMessage="banking_machine.address.length.max"
     * )
     */
    protected $address;

    /**
     * @ORM\Column(type="string", length=500)
     *
     * @Assert\NotBlank(message="banking_machine.location.not_blank")
     * @Assert\Length(
     *      min=2,
     *      max=500,
     *      minMessage="banking_machine.location.length.min",
     *      maxMessage="banking_machine.location.length.max"
     * )
     */
    protected $location;

    public function __construct()
    {
        $this->bankingMachineSyncs  = new ArrayCollection;
        $this->bankingMachineEvents = new ArrayCollection;
        $this->operators            = new ArrayCollection;
        $this->accountGroups        = new ArrayCollection;
        $this->transactions         = new ArrayCollection;
    }

    public function __toString()
    {
        return ( $this->serial ) ? $this->serial : "";
    }

    /**
     * Set serial
     *
     * @param string $serial
     *
     * @return BankingMachine
     */
    public function setSerial($serial)
    {
        $this->serial = $serial;

        return $this;
    }

    /**
     * Get serial
     *
     * @return string
     */
    public function getSerial()
    {
        return $this->serial;
    }

    /**
     * Set login
     *
     * @param string $login
     *
     * @return BankingMachine
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * Get login
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return BankingMachine
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return BankingMachine
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
     * Set address
     *
     * @param string $address
     *
     * @return BankingMachine
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set location
     *
     * @param string $location
     *
     * @return BankingMachine
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set organization
     *
     * @param \AppBundle\Entity\Organization\Organization $organization
     *
     * @return BankingMachine
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
     * Add bankingMachineSync
     *
     * @param \AppBundle\Entity\BankingMachine\BankingMachineSync $bankingMachineSync
     *
     * @return BankingMachine
     */
    public function addBankingMachineSync(\AppBundle\Entity\BankingMachine\BankingMachineSync $bankingMachineSync)
    {
        $bankingMachineSync->setBankingMachine($this);
        $this->bankingMachineSyncs[] = $bankingMachineSync;

        return $this;
    }

    /**
     * Remove bankingMachineSync
     *
     * @param \AppBundle\Entity\BankingMachine\BankingMachineSync $bankingMachineSync
     */
    public function removeBankingMachineSync(\AppBundle\Entity\BankingMachine\BankingMachineSync $bankingMachineSync)
    {
        $this->bankingMachineSyncs->removeElement($bankingMachineSync);
    }

    /**
     * Get bankingMachineSyncs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBankingMachineSyncs()
    {
        return $this->bankingMachineSyncs;
    }

    /**
     * Add bankingMachineEvent
     *
     * @param \AppBundle\Entity\BankingMachine\BankingMachineEvent $bankingMachineEvent
     *
     * @return BankingMachine
     */
    public function addBankingMachineEvent(\AppBundle\Entity\BankingMachine\BankingMachineEvent $bankingMachineEvent)
    {
        $bankingMachineEvent->setBankingMachine($this);
        $this->bankingMachineEvents[] = $bankingMachineEvent;

        return $this;
    }

    /**
     * Remove bankingMachineEvent
     *
     * @param \AppBundle\Entity\BankingMachine\BankingMachineEvent $bankingMachineEvent
     */
    public function removeBankingMachineEvent(\AppBundle\Entity\BankingMachine\BankingMachineEvent $bankingMachineEvent)
    {
        $this->bankingMachineEvents->removeElement($bankingMachineEvent);
    }

    /**
     * Get bankingMachineEvents
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBankingMachineEvents()
    {
        return $this->bankingMachineEvents;
    }

    /**
     * Add operator
     *
     * @param \AppBundle\Entity\Operator\Operator $operator
     *
     * @return BankingMachine
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
     * Add accountGroup
     *
     * @param \AppBundle\Entity\Account\AccountGroup $accountGroup
     *
     * @return BankingMachine
     */
    public function addAccountGroup(\AppBundle\Entity\Account\AccountGroup $accountGroup)
    {
        $this->accountGroups[] = $accountGroup;

        return $this;
    }

    /**
     * Remove accountGroup
     *
     * @param \AppBundle\Entity\Account\AccountGroup $accountGroup
     */
    public function removeAccountGroup(\AppBundle\Entity\Account\AccountGroup $accountGroup)
    {
        $this->accountGroups->removeElement($accountGroup);
    }

    /**
     * Get accountGroups
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAccountGroups()
    {
        return $this->accountGroups;
    }

    /**
     * Add transaction
     *
     * @param \AppBundle\Entity\Transaction\Transaction $transaction
     *
     * @return BankingMachine
     */
    public function addTransaction(\AppBundle\Entity\Transaction\Transaction $transaction)
    {
        $transaction->setBankingMachine($this);
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

    /*-------------------------------------------------------------------------
    | INTERFACE IMPLEMENTATION
    |------------------------------------------------------------------------*/

    static public function getProperties()
    {
        return [
            self::PROPERTY_ID,
            self::PROPERTY_SERIAL,
            self::PROPERTY_NAME,
            self::PROPERTY_ADDRESS,
            self::PROPERTY_LOCATION,
        ];
    }
}
