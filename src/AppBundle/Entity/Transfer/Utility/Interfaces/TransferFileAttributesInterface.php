<?php
// src/AppBundle/Entity/Transfer/Utility/Interfaces/TransferFileAttributesInterface.php
namespace AppBundle\Entity\Transfer\Utility\Interfaces;

interface TransferFileAttributesInterface
{
    const ROOT_DIR = '';

    const TRANSFER_DIR_FORMAT = 'Ymd';

    // Filename prefix
    const PREFIX = 'SM';

    // File extension
    const EXTENSION = 'TXT';

    // Filename length without prefix
    const LENGTH = 6;
}
