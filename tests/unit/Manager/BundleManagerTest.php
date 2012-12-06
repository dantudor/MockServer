<?php

use MockServer\Manager\BundleManager;
use MockFs\MockFs;
use Symfony\Component\Filesystem\Filesystem;

class BundleManagerTest extends PHPUnit_Framework_TestCase
{
    protected $skeletonDirectory;

    public function setUp()
    {
        $this->skeletonDirectory = __DIR__ . '/../../../src/MockServer/Resources/Skeleton/SymfonyBundle';
    }

    /**
     * @covers \MockServer\Manager\BundleManager
     * @expectedException \MockServer\Exception\BundleGenerationException
     */
    public function testBundleManagerThrowsExceptionWhenTargetDirectoryIsNotEmpty()
    {
        $namespace = 'MockNamespace';
        $directory = '/targetDirectory';
        $target = 'mfs:/' . $directory;

        $mockFs = new MockFs();
        $mockFs->getFileSystem()->addFile('Im in your way.txt', '', $directory . '/' . $namespace);

        $bundleManager = new BundleManager(new Filesystem(), '/');
        $bundleManager->generate($namespace, 'MockBundle', $target);
    }

    /**
     * @covers \MockServer\Manager\BundleManager
     */
    public function testBundleManagerGeneratesBundleClass()
    {
        $namespace = 'MockNamespace';
        $bundle = 'MockBundle';
        $target = 'mfs://';
$contents = "<?php

namespace MockNamespace;

use Symfony\\Component\\HttpKernel\\Bundle\\Bundle;

class MockBundle extends Bundle
{
}
";
        $mockFs = new MockFs();

        $bundleManager = new BundleManager(new Filesystem(), $this->skeletonDirectory);
        $bundleManager->generate($namespace, $bundle, $target);

        /** @var $newFile \MockFs\Object\File */
        $newFile = $mockFs->getFileSystem()->getChildByPath('/MockNamespace/MockBundle.php');

        $this->assertSame($contents, $newFile->getContents());
    }
}