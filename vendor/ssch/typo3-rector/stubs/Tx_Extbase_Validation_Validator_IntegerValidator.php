<?php

namespace RectorPrefix20210725;

if (\class_exists('Tx_Extbase_Validation_Validator_IntegerValidator')) {
    return;
}
class Tx_Extbase_Validation_Validator_IntegerValidator
{
}
\class_alias('Tx_Extbase_Validation_Validator_IntegerValidator', 'Tx_Extbase_Validation_Validator_IntegerValidator', \false);
