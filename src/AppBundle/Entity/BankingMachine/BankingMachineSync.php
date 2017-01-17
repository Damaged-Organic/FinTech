<?php
// src/AppBundle/Entity/BankingMachine/BankingMachineSync.php
namespace AppBundle\Entity\BankingMachine;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait;

/**
 * @ORM\Table(name="banking_machines_sync")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\BankingMachine\Repository\BankingMachineSyncRepository")
 */
class BankingMachineSync
{
    use IdMapperTrait;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BankingMachine\BankingMachine", inversedBy="bankingMachineSyncs")
     * @ORM\JoinColumn(name="banking_machine_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $bankingMachine;

    /**
     * @ORM\Column(type="string", length=64)
     */
    protected $syncId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $type;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $syncedAt;

    /**
     * @ORM\Column(type="string", length=64)
     */
    protected $checksum;

    /**
     * @ORM\Column(type="text")
     */
    protected $data;

    public function __toString()
    {
        return ( $this->syncId ) ? $this->syncId : "";
    }

    /**
     * Set syncId
     *
     * @param string $syncId
     *
     * @return BankingMachineSync
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
     * Set type
     *
     * @param string $type
     *
     * @return BankingMachineSync
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set syncedAt
     *
     * @param \DateTime $syncedAt
     *
     * @return BankingMachineSync
     */
    public function setSyncedAt($syncedAt)
    {
        $this->syncedAt = $syncedAt;

        return $this;
    }

    /**
     * Get syncedAt
     *
     * @return \DateTime
     */
    public function getSyncedAt()
    {
        return $this->syncedAt;
    }

    /**
     * Set checksum
     *
     * @param string $checksum
     *
     * @return BankingMachineSync
     */
    public function setChecksum($checksum)
    {
        $this->checksum = $checksum;

        return $this;
    }

    /**
     * Get checksum
     *
     * @return string
     */
    public function getChecksum()
    {
        return $this->checksum;
    }

    /**
     * Set data
     *
     * @param string $data
     *
     * @return BankingMachineSync
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set bankingMachine
     *
     * @param \AppBundle\Entity\BankingMachine\BankingMachine $bankingMachine
     *
     * @return BankingMachineSync
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
}
