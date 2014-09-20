<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel {

    public function registerBundles() {
        $bundles = array(
        );

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader) {

    }

}
