<?php
// src/AppBundle/Entity/Transfer/Utility/Interfaces/TransferAttributesInterface.php
namespace AppBundle\Entity\Transfer\Utility\Interfaces;

interface TransferAttributesInterface
{
    // String fields encoding
    const ENCODING = 'CP866';

    // Files specific newline character
    const NEWLINE = "\r\n";

    // [1]
    const MFO_OF_BANK_A_TYPE   = 'integer';
    const MFO_OF_BANK_A_LENGTH = 9;

    // [2]
    const PERSONAL_ACCOUNT_OF_BANK_A_TYPE   = 'bigint';
    const PERSONAL_ACCOUNT_OF_BANK_A_LENGTH = 14;

    // [3]
    const MFO_OF_BANK_B_TYPE   = 'integer';
    const MFO_OF_BANK_B_LENGTH = 9;

    // [4]
    const PERSONAL_ACCOUNT_OF_BANK_B_TYPE   = 'bigint';
    const PERSONAL_ACCOUNT_OF_BANK_B_LENGTH = 14;

    // [5]
    const DEBIT_CREDIT_PAYMENT_FLAG_TYPE   = 'boolean';
    // Not used in annotation configuration due to default boolean length
    const DEBIT_CREDIT_PAYMENT_FLAG_LENGTH = 1;

    // [6]
    const PAYMENT_AMOUNT_TYPE = 'integer';
    const PAYMENT_AMOUNT_LENGTH = 16;

    // [7]
    const PAYMENT_DOCUMENT_TYPE_TYPE   = 'integer';
    const PAYMENT_DOCUMENT_TYPE_LENGTH = 2;

    // [8]
    // Official documentation suggests 'string' type of this field,
    // however, real-world document suggests padding it with spaces
    // from the beginning of the value, which is how actually 'integer'
    // is supposed to be formatted - and field includes numbers only
    const PAYMENT_OPERATIONAL_NUMBER_TYPE   = 'integer';
    const PAYMENT_OPERATIONAL_NUMBER_LENGTH = 10;

    // [9]
    const PAYMENT_CURRENCY_TYPE   = 'integer';
    const PAYMENT_CURRENCY_LENGTH = 3;

    // [10]
    const PAYMENT_DOCUMENT_DATE_TYPE   = 'date';
    // Not used in annotation configuration due to default date length
    const PAYMENT_DOCUMENT_DATE_LENGTH = 6;

    // [11]
    const PAYMENT_DOCUMENT_ARRIVAL_DATE_TO_BANK_A_TYPE   = 'date';
    // Not used in annotation configuration due to default date length
    const PAYMENT_DOCUMENT_ARRIVAL_DATE_TO_BANK_A_LENGTH = 6;

    // [12]
    const PAYER_NAME_OF_CLIENT_A_TYPE   = 'string';
    const PAYER_NAME_OF_CLIENT_A_LENGTH = 38;

    // [13]
    const PAYER_NAME_OF_CLIENT_B_TYPE   = 'string';
    const PAYER_NAME_OF_CLIENT_B_LENGTH = 38;

    // [14]
    const PAYMENT_DESTINATION_TYPE   = 'string';
    const PAYMENT_DESTINATION_LENGTH = 160;

    // [15]
    const SUPPORTING_PROPS_TYPE   = 'string';
    const SUPPORTING_PROPS_LENGTH = 60;

    // [16]
    const PAYMENT_DESTINATION_CODE_TYPE   = 'string';
    const PAYMENT_DESTINATION_CODE_LENGTH = 3;

    // [17]
    const STRINGS_NUMBER_IN_BLOCK_TYPE   = 'string';
    const STRINGS_NUMBER_IN_BLOCK_LENGTH = 2;

    // [18]
    const CLIENT_IDENTIFIER_A_TYPE   = 'string';
    const CLIENT_IDENTIFIER_A_LENGTH = 14;

    // [19]
    const CLIENT_IDENTIFIER_B_TYPE   = 'string';
    const CLIENT_IDENTIFIER_B_LENGTH = 14;
}
