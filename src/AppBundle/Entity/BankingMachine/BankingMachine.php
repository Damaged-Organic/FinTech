<?php
// src/AppBundle/Entity/BankingMachine/BankingMachine.php
namespace AppBundle\Entity\BankingMachine;

use Symfony\Component\Validator\Constraints as Assert,
    Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait,
    AppBundle\Entity\Utility\Traits\DoctrineMapping\PseudoDeleteMapperTrait;

/**
 * @ORM\Table(name="banking_machines")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\BankingMachine\Repository\BankingMachineRepository")
 *
 * @UniqueEntity(fields="serial", message="banking_machine.serial.unique")
 * @UniqueEntity(fields="login", message="banking_machine.login.unique")
 */
class BankingMachine
{
    use IdMapperTrait, PseudoDeleteMapperTrait;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Organization\Organization", inversedBy="bankingMachines")
     * @ORM\JoinColumn(name="organization_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $organization;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Operator\Operator", mappedBy="bankingMachine")
     */
    protected $operators;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Account\AccountGroup", inversedBy="bankingMachines")
     * @ORM\JoinTable(name="banking_machines_accounts_groups")
     */
    protected $accountGroups;

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

    public function __construct()
    {
        $this->operators            = new ArrayCollection;
        $this->accountGroups        = new ArrayCollection;
        $this->bankingMachineSyncs  = new ArrayCollection;
        $this->bankingMachineEvents = new ArrayCollection;
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
     * Add operator
     *
     * @param \AppBundle\Entity\Operator\Operator $operator
     *
     * @return BankingMachine
     */
    public function addOperator(\AppBundle\Entity\Operator\Operator $operator)
    {
        $operator->setBankingMachine($this);
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
}
