<?php

declare (strict_types=1);
namespace RectorPrefix20211210\Doctrine\Inflector;

interface WordInflector
{
    /**
     * @param string $word
     */
    public function inflect($word) : string;
}
