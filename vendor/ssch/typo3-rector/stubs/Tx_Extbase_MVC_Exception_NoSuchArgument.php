<?php

namespace RectorPrefix20210811;

if (\class_exists('Tx_Extbase_MVC_Exception_NoSuchArgument')) {
    return;
}
class Tx_Extbase_MVC_Exception_NoSuchArgument
{
}
\class_alias('Tx_Extbase_MVC_Exception_NoSuchArgument', 'Tx_Extbase_MVC_Exception_NoSuchArgument', \false);
