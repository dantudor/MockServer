<?php

use MockServer\Manager\BundleManager;
use MockFs\MockFs;
use Symfony\Component\Filesystem\Filesystem;
use Sensio\Bundle\GeneratorBundle\Generator\Generator;

class BundleManagerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var TestGenerator
     */
    protected $generator;

    /**
     * @var string
     */
    protected $skeletonDirectory;

    /**
     * @var \MockFs\MockFs
     */
    protected $mockFs;

    /**
     * @var array
     */
    protected $parameters;

    /**
     * Setup
     */
    public function setUp()
    {
        $this->generator = new TestGenerator();
        $this->mockFs = new MockFs();
        $this->skeletonDirectory = __DIR__ . '/../../../src/MockServer/Resources/Skeleton/SymfonyBundle';

        $this->parameters = array(
            'namespace' => 'MockNamespace',
            'bundle' => 'MockBundle',
            'bundle_basename' => 'Mock',
            'extension_alias' => 'mock',
        );
    }

    /**
     * @covers \MockServer\Manager\BundleManager
     * @expectedException \MockServer\Exception\BundleGenerationException
     */
    public function testBundleManagerThrowsExceptionWhenTargetDirectoryIsNotEmpty()
    {
        $directory = '/targetDirectory';
        $target = 'mfs:/' . $directory;


        $this->mockFs->getFileSystem()->addFile('Im in your way.txt', '', $directory . '/' . $this->parameters['namespace']);

        $bundleManager = new BundleManager(new Filesystem(), '/');
        $bundleManager->generate($this->parameters['namespace'], 'MockBundle', $target);
    }

    /**
     * @covers \MockServer\Manager\BundleManager
     */
    public function testBundleManagerGeneratesBundle()
    {
        $expectedContent = $this->generator->render($this->skeletonDirectory, 'Bundle.php', $this->parameters);

        $bundleManager = new BundleManager(new Filesystem(), $this->skeletonDirectory);
        $bundleManager->generate($this->parameters['namespace'], $this->parameters['bundle'], 'mfs://');

        $this->assertSame($expectedContent, $this->mockFs->getFileSystem()->getChildByPath('/MockNamespace/MockBundle.php')->getContents());
    }

    /**
     * @covers \MockServer\Manager\BundleManager
     */
    public function testBundleManagerGeneratesDefaultController()
    {
        $expectedContent = $this->generator->render($this->skeletonDirectory, 'DefaultController.php', $this->parameters);

        $bundleManager = new BundleManager(new Filesystem(), $this->skeletonDirectory);
        $bundleManager->generate($this->parameters['namespace'], $this->parameters['bundle'], 'mfs://');

        $this->assertSame($expectedContent, $this->mockFs->getFileSystem()->getChildByPath('/MockNamespace/Controller/DefaultController.php')->getContents());
    }

    /**
     * @covers \MockServer\Manager\BundleManager
     */
    public function testBundleManagerGeneratesServicesYml()
    {
        $expectedContent = $this->generator->render($this->skeletonDirectory, 'services.yml', $this->parameters);

        $bundleManager = new BundleManager(new Filesystem(), $this->skeletonDirectory);
        $bundleManager->generate($this->parameters['namespace'], $this->parameters['bundle'], 'mfs://');

        $this->assertSame($expectedContent, $this->mockFs->getFileSystem()->getChildByPath('/MockNamespace/Resources/config/services.yml')->getContents());
    }

    /**
     * @covers \MockServer\Manager\BundleManager
     */
    public function testBundleManagerGeneratesIndexHtmlTwig()
    {
        $expectedContent = $this->generator->render($this->skeletonDirectory, 'index.html.twig', $this->parameters);

        $bundleManager = new BundleManager(new Filesystem(), $this->skeletonDirectory);
        $bundleManager->generate($this->parameters['namespace'], $this->parameters['bundle'], 'mfs://');

        $this->assertSame($expectedContent, $this->mockFs->getFileSystem()->getChildByPath('/MockNamespace/Resources/views/Default/index.html.twig')->getContents());
    }

    /**
     * @covers \MockServer\Manager\BundleManager
     */
    public function testBundleManagerGeneratesExtension()
    {
        $expectedContent = $this->generator->render($this->skeletonDirectory, 'Extension.php', $this->parameters);

        $bundleManager = new BundleManager(new Filesystem(), $this->skeletonDirectory);
        $bundleManager->generate($this->parameters['namespace'], $this->parameters['bundle'], 'mfs://');

        $this->assertSame($expectedContent, $this->mockFs->getFileSystem()->getChildByPath('/MockNamespace/DependencyInjection/' . $this->parameters['bundle_basename'] . 'Extension.php')->getContents());
    }

    /**
     * @covers \MockServer\Manager\BundleManager
     */
    public function testBundleManagerGeneratesServer()
    {
        $expectedContent = $this->generator->render($this->skeletonDirectory, 'Server.php', $this->parameters);

        $bundleManager = new BundleManager(new Filesystem(), $this->skeletonDirectory);
        $bundleManager->generate($this->parameters['namespace'], $this->parameters['bundle'], 'mfs://');

        $this->assertSame($expectedContent, $this->mockFs->getFileSystem()->getChildByPath('/MockNamespace/Server/' . $this->parameters['bundle_basename'] . 'Server.php')->getContents());
    }

    /**
     * @covers \MockServer\Manager\BundleManager
     */
    public function testBundleManagerGeneratesAppKernel()
    {
        $expectedContent = $this->generator->render($this->skeletonDirectory, 'AppKernel.php', $this->parameters);

        $bundleManager = new BundleManager(new Filesystem(), $this->skeletonDirectory);
        $bundleManager->generate($this->parameters['namespace'], $this->parameters['bundle'], 'mfs://');

        $this->assertSame($expectedContent, $this->mockFs->getFileSystem()->getChildByPath('/MockNamespace/app/AppKernel.php')->getContents());
    }

    /**
     * @covers \MockServer\Manager\BundleManager
     */
    public function testBundleManagerGeneratesConfigYml()
    {
        $expectedContent = $this->generator->render($this->skeletonDirectory, 'config.yml', $this->parameters);

        $bundleManager = new BundleManager(new Filesystem(), $this->skeletonDirectory);
        $bundleManager->generate($this->parameters['namespace'], $this->parameters['bundle'], 'mfs://');

        $this->assertSame($expectedContent, $this->mockFs->getFileSystem()->getChildByPath('/MockNamespace/app/config/config.yml')->getContents());
    }

    /**
     * @covers \MockServer\Manager\BundleManager
     */
    public function testBundleManagerGeneratesRoutingYml()
    {
        $expectedContent = $this->generator->render($this->skeletonDirectory, 'routing.yml', $this->parameters);

        $bundleManager = new BundleManager(new Filesystem(), $this->skeletonDirectory);
        $bundleManager->generate($this->parameters['namespace'], $this->parameters['bundle'], 'mfs://');

        $this->assertSame($expectedContent, $this->mockFs->getFileSystem()->getChildByPath('/MockNamespace/app/config/routing.yml')->getContents());
    }
}

class TestGenerator extends Generator
{
    public function render($skeletonDirectory, $template, $parameters)
    {
        return parent::render($skeletonDirectory, $template, $parameters);
    }
}