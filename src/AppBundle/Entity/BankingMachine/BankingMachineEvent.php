<?php
// src/AppBundle/Entity/BankingMachine/BankingMachineEvent.php
namespace AppBundle\Entity\BankingMachine;

use Doctrine\ORM\Mapping as ORM;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait;

/**
 * @ORM\Table(name="banking_machines_events")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\BankingMachine\Repository\BankingMachineEventRepository")
 */
class BankingMachineEvent
{
    use IdMapperTrait;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BankingMachine\BankingMachine", inversedBy="bankingMachineEvents")
     * @ORM\JoinColumn(name="banking_machine_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $bankingMachine;

    /**
     * @ORM\Column(type="integer")
     */
    protected $syncId;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $occurredAt;

    /**
     * @ORM\Column(type="string", length=32)
     */
    protected $type;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $code;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    protected $message;

    public function __toString()
    {
        return ( $this->syncId ) ? $this->syncId : "";
    }

    /**
     * Set syncId
     *
     * @param integer $syncId
     *
     * @return BankingMachineEvent
     */
    public function setSyncId($syncId)
    {
        $this->syncId = $syncId;

        return $this;
    }

    /**
     * Get syncId
     *
     * @return integer
     */
    public function getSyncId()
    {
        return $this->syncId;
    }

    /**
     * Set occurredAt
     *
     * @param \DateTime $occurredAt
     *
     * @return BankingMachineEvent
     */
    public function setOccurredAt($occurredAt)
    {
        $this->occurredAt = $occurredAt;

        return $this;
    }

    /**
     * Get occurredAt
     *
     * @return \DateTime
     */
    public function getOccurredAt()
    {
        return $this->occurredAt;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return BankingMachineEvent
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
     * Set code
     *
     * @param integer $code
     *
     * @return BankingMachineEvent
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return integer
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return BankingMachineEvent
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set bankingMachine
     *
     * @param \AppBundle\Entity\BankingMachine\BankingMachine $bankingMachine
     *
     * @return BankingMachineEvent
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
