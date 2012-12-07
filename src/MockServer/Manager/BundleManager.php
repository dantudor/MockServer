<?php

namespace MockServer\Manager;

use Sensio\Bundle\GeneratorBundle\Generator\Generator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\DependencyInjection\Container;
use MockServer\Exception\BundleGenerationException;

/**
 * Bundle Manager
 *
 * The bundle manager is responsible for generating new bundle instances into projects.
 */
class BundleManager extends Generator
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var string
     */
    protected $skeletonDir;

    /**
     * Constructor
     *
     * @param Filesystem $filesystem
     * @param $skeletonDir
     */
    public function __construct(Filesystem $filesystem, $skeletonDir)
    {
        $this->filesystem = $filesystem;
        $this->skeletonDir = $skeletonDir;
    }

    /**
     * @param $namespace
     * @param $bundle
     * @param $dir
     * @throws \MockServer\Exception\BundleGenerationException
     */
    public function generate($namespace, $bundle, $dir)
    {
        $dir .= '/' . strtr($namespace, '\\', '/');
        if ($this->filesystem->exists($dir)) {
            throw new BundleGenerationException(sprintf('Unable to generate the bundle as the target directory "%s" is not empty.', realpath($dir)));
        }

        $basename = substr($bundle, 0, -6);
        $parameters = array(
            'namespace' => $namespace,
            'bundle' => $bundle,
            'bundle_basename' => $basename,
            'extension_alias' => Container::underscore($basename),
        );

        $this->renderFile($this->skeletonDir, 'Bundle.php', $dir . '/' . $bundle . '.php', $parameters);
        $this->renderFile($this->skeletonDir, 'DefaultController.php', $dir . '/Controller/DefaultController.php', $parameters);
        $this->renderFile($this->skeletonDir, 'services.yml', $dir . '/Resources/config/services.yml', $parameters);
        $this->renderFile($this->skeletonDir, 'index.html.twig', $dir . '/Resources/views/Default/index.html.twig', $parameters);
        $this->renderFile($this->skeletonDir, 'Extension.php', $dir . '/DependencyInjection/' . $basename . 'Extension.php', $parameters);
        $this->renderFile($this->skeletonDir, 'Server.php', $dir . '/Server/' . $basename . 'Server.php', $parameters);
        $this->renderFile($this->skeletonDir, 'AppKernel.php', $dir . '/app/AppKernel.php', $parameters);
        $this->renderFile($this->skeletonDir, 'config.yml', $dir . '/app/config/config.yml', $parameters);
        $this->renderFile($this->skeletonDir, 'routing.yml', $dir . '/app/config/routing.yml', $parameters);
        $this->renderFile($this->skeletonDir, 'security.yml', $dir . '/app/config/security.yml', $parameters);
    }
}
