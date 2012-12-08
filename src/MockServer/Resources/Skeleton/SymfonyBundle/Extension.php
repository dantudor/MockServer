<?php

namespace {{ namespace }}\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class {{ bundle_basename }}Extension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        foreach ($configs as $config) {
            $this->setParameters($config, $container, '{{ extension_alias }}');
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * Recursively set config parameters in the container.
     *
     * @param array $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param $path
     */
    private function setParameters(array $config, ContainerBuilder $container, $path)
    {
        foreach ($config as $key => $value) {
            if (is_array($value)) {
                $this->setParameters($value, $container, "$path.$key");
            }

            $container->setParameter("$path.$key", $value);
        }
    }
}
