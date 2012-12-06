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

//        $basename = substr($bundle, 0, -6);
        $parameters = array(
            'namespace' => $namespace,
            'bundle' => $bundle,
//            'format' => 'yml',
//            'bundle_basename' => $basename,
//            'extension_alias' => Container::underscore($basename),
        );

        $this->renderFile($this->skeletonDir, 'Bundle.php.twig', $dir . '/' . $bundle . '.php', $parameters);
        $this->renderFile($this->skeletonDir, 'DefaultController.php.twig', $dir . '/Controller/DefaultController.php', $parameters);
    }
}