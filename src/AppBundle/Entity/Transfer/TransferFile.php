<?php
// src/AppBundle/Entity/Transfer/TransferFile.php
namespace AppBundle\Entity\Transfer;

use Doctrine\ORM\Mapping as ORM;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait,
    AppBundle\Entity\Transfer\Utility\Interfaces\TransferFileAttributesInterface;

/**
 * @ORM\Table(name="transfers_files")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Transfer\Repository\TransferFileRepository")
 */
class TransferFile implements TransferFileAttributesInterface
{
    use IdMapperTrait;

    /**
     * @ORM\OneToOne(targetEntity="Transfer", mappedBy="transferFile")
     */
    protected $transfer;

    /**
     * @ORM\Column(type = "datetime")
     */
    protected $datetime;

    /**
     * @ORM\Column(type = "date")
     */
    protected $dirname;

    /**
     * @ORM\Column(type = "string", length = 12);
     */
    protected $filename;

    /**
     * @ORM\Column(type = "text", nullable = true);
     */
    protected $recordString;

    /**
     * @ORM\Column(type = "boolean");
     */
    protected $isSynced;

    /**
     * To string
     */
    public function __toString()
    {
        return (string)$this->filename ?: 'TransferFile';
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->isSynced = FALSE;
    }

    /**
     * Set datetime
     *
     * @param \DateTime $datetime
     *
     * @return TransferFile
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;

        return $this;
    }

    /**
     * Get datetime
     *
     * @return \DateTime
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * Set dirname
     *
     * @param \DateTime $dirname
     *
     * @return TransferFile
     */
    public function setDirname($dirname)
    {
        $this->dirname = $dirname;

        return $this;
    }

    /**
     * Get dirname
     *
     * @return \DateTime
     */
    public function getDirname()
    {
        return $this->dirname;
    }

    /**
     * Set filename
     *
     * @param string $filename
     *
     * @return TransferFile
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set recordString
     *
     * @param string $recordString
     *
     * @return TransferFile
     */
    public function setRecordString($recordString)
    {
        $this->recordString = $recordString;

        return $this;
    }

    /**
     * Get recordString
     *
     * @return string
     */
    public function getRecordString()
    {
        return $this->recordString;
    }

    /**
     * Set isSynced
     *
     * @param boolean $isSynced
     *
     * @return TransferFile
     */
    public function setIsSynced($isSynced)
    {
        $this->isSynced = $isSynced;

        return $this;
    }

    /**
     * Get isSynced
     *
     * @return boolean
     */
    public function getIsSynced()
    {
        return $this->isSynced;
    }

    /**
     * Set transfer
     *
     * @param \AppBundle\Entity\Transfer\Transfer $transfer
     *
     * @return TransferFile
     */
    public function setTransfer(\AppBundle\Entity\Transfer\Transfer $transfer = null)
    {
        $this->transfer = $transfer;

        return $this;
    }

    /**
     * Get transfer
     *
     * @return \AppBundle\Entity\Transfer\Transfer
     */
    public function getTransfer()
    {
        return $this->transfer;
    }
}
