<?php
// src/SyncBundle/Service/BankingServer/Transfer/Formatter.php
namespace SyncBundle\Service\BankingServer\Transfer;

use DateTime;

class Formatter
{
    public function formatRecordField($value, $length, $encoding = NULL)
    {
        list($value, $padding) = ( is_string($value) )
            ? $this->formatStringValue($value, $encoding)
            : $this->formatNonStringValue($value)
        ;

        return str_pad($value, $length, ' ', $padding);
    }

    private function formatStringValue($value, $encoding)
    {
        $value = mb_convert_encoding(
            (string)$value, $encoding, 'UTF-8'
        );

        $padding = STR_PAD_RIGHT;

        return [$value, $padding];
    }

    private function formatNonStringValue($value)
    {
        $padding = STR_PAD_LEFT;

        return [$value, $padding];
    }

    public function formatFilename($filename, $prefix, $length, $extension)
    {
        return $prefix . sprintf("%0{$length}d", $filename) . '.' . $extension;
    }

    public function formatRecordString(array $record, $newline)
    {
        return implode('', $record) . $newline;
    }

    public function formatDirname(DateTime $dirname, $rootDir, $dirFormat)
    {
        return implode('/', [$rootDir, $dirname->format($dirFormat)]);
    }
}
