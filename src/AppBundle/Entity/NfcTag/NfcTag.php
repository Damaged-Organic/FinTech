<?php
// src/AppBundle/Entity/NfcTag/NfcTag.php
namespace AppBundle\Entity\NfcTag;

use DateTime;

use Symfony\Component\Validator\Constraints as Assert,
    Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait,
    AppBundle\Entity\Utility\Traits\DoctrineMapping\PseudoDeleteMapperTrait,
    AppBundle\Validator\Constraints as CustomAssert,
    AppBundle\Entity\NfcTag\Properties\NfcTagPropertiesInterface;

/**
 * @ORM\Table(name="nfc_tags")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\NfcTag\Repository\NfcTagRepository")
 *
 * @UniqueEntity(fields="number", message="nfc_tag.number.unique")
 * @UniqueEntity(fields="code", message="nfc_tag.code.unique")
 */
class NfcTag implements NfcTagPropertiesInterface
{
    use IdMapperTrait, PseudoDeleteMapperTrait;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Operator\Operator", inversedBy="nfcTag")
     * @ORM\JoinColumn(name="operator_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $operator;

    /**
     * @ORM\Column(type="string", length=8, unique=true)
     *
     * @Assert\NotBlank(message="nfc_tag.number.not_blank")
     * @CustomAssert\IsNfcTagNumber
     */
    protected $number;

    /**
     * @ORM\Column(type="string", length=32, unique=true)
     *
     * @Assert\NotBlank(message="nfc_tag.code.not_blank")
     * @CustomAssert\IsNfcTagCode
     */
    protected $code;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $isActivated = FALSE;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $activatedAt;

    public function __toString()
    {
        return ( $this->number ) ? $this->number : static::class;
    }

    /**
     * Set number
     *
     * @param string $number
     *
     * @return NfcTag
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return NfcTag
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set isActivated
     *
     * @param boolean $isActivated
     *
     * @return NfcTag
     */
    public function setIsActivated($isActivated)
    {
        $this->isActivated = $isActivated;

        return $this;
    }

    /**
     * Get isActivated
     *
     * @return boolean
     */
    public function getIsActivated()
    {
        return $this->isActivated;
    }

    /**
     * Set activatedAt
     *
     * @param \DateTime $activatedAt
     *
     * @return NfcTag
     */
    public function setActivatedAt($activatedAt)
    {
        $this->activatedAt = $activatedAt;

        return $this;
    }

    /**
     * Get activatedAt
     *
     * @return \DateTime
     */
    public function getActivatedAt()
    {
        return $this->activatedAt;
    }

    /**
     * Set operator
     *
     * @param \AppBundle\Entity\Operator\Operator $operator
     *
     * @return NfcTag
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

    /*-------------------------------------------------------------------------
    | ACTIVATION
    |------------------------------------------------------------------------*/

    public function activate()
    {
        if( $this->getIsActivated() === TRUE )
            return;
        $this
            ->setIsActivated(TRUE)
            ->setActivatedAt(new DateTime)
        ;
    }
    public function deactivate()
    {
        if( $this->getIsActivated() === FALSE )
            return;
        $this
            ->setIsActivated(FALSE)
            ->setActivatedAt(NULL)
        ;
    }
}
