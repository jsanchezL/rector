<?php

declare (strict_types=1);
namespace RectorPrefix20210829\Symplify\EasyTesting\HttpKernel;

use RectorPrefix20210829\Symfony\Component\Config\Loader\LoaderInterface;
use RectorPrefix20210829\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class EasyTestingKernel extends \RectorPrefix20210829\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel
{
    /**
     * @param \Symfony\Component\Config\Loader\LoaderInterface $loader
     */
    public function registerContainerConfiguration($loader) : void
    {
        $loader->load(__DIR__ . '/../../config/config.php');
    }
}
