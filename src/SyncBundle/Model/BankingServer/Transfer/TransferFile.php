<?php
// src/SyncBundle/Model/BankingServer/Transfer/TransferFile.php
namespace SyncBundle\Model\BankingServer\Transfer;

use AppBundle\Entity\Transfer\TransferFile as TransferFileEntity,
    AppBundle\Entity\Transfer\Utility\Interfaces\TransferFileAttributesInterface;

use SyncBundle\Service\BankingServer\Transfer\Formatter;

class TransferFile implements TransferFileAttributesInterface
{
    private $transferFile;
    private $formatter;

    public function __construct(
        TransferFileEntity $transferFileEntity,
        Formatter $formatter
    ) {
        $this->transferFile = $transferFileEntity;
        $this->formatter    = $formatter;
    }

    /*-------------------------------------------------------------------------
    | Transfer File
    |------------------------------------------------------------------------*/

    /**
     * Get dirname
     *
     * @return string
     */
    public function getDirname()
    {
        return $this->formatter->formatDirname(
            $this->transferFile->getDirname(), self::ROOT_DIR, self::TRANSFER_DIR_FORMAT
        );
    }

    /**
     * Get filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->formatter->formatFilename(
            $this->transferFile->getFilename(), self::PREFIX, self::LENGTH, self::EXTENSION
        );
    }

    /*-------------------------------------------------------------------------
    | End | Transfer File
    |------------------------------------------------------------------------*/
}
