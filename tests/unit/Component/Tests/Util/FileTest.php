<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Util;

use Imi\Test\BaseTest;
use Imi\Util\File;
use Imi\Util\Imi;

/**
 * @testdox Imi\Util\File
 */
class FileTest extends BaseTest
{
    /**
     * @testdox enum
     */
    public function testEnum(): void
    {
        $path = \dirname(__DIR__, 2) . '/Util/File';
        $expectedFiles = [
            ['pathName' => $path . '/1.txt', 'pathName2' => $path . '/1.txt', 'fileName' => '1.txt'],
            ['pathName' => $path . '/2.php', 'pathName2' => $path . '/2.php', 'fileName' => '2.php'],
            ['pathName' => $path . '/a/a-1/a-1.txt', 'pathName2' => $path . '/a/a-1/a-1.txt', 'fileName' => 'a-1.txt'],
            ['pathName' => $path . '/b/b.php', 'pathName2' => $path . '/b/b.php', 'fileName' => 'b.php'],
        ];

        $files = [];
        foreach (File::enum($path) as $file)
        {
            $files[] = [
                'pathName'  => $file->getPathName(),
                'pathName2' => (string) $file,
                'fileName'  => $file->getFileName(),
            ];
        }
        $this->assertEqualsCanonicalizing($expectedFiles, $files);
    }

    /**
     * @testdox enumAll
     */
    public function testEnumAll(): void
    {
        $path = \dirname(__DIR__, 2) . '/Util/File';
        $expectedFiles = [
            ['pathName' => $path . '/1.txt', 'pathName2' => $path . '/1.txt', 'fileName' => '1.txt'],
            ['pathName' => $path . '/2.php', 'pathName2' => $path . '/2.php', 'fileName' => '2.php'],
            ['pathName' => $path . '/a/a-1', 'pathName2' => $path . '/a/a-1', 'fileName' => 'a-1'],
            ['pathName' => $path . '/a', 'pathName2' => $path . '/a', 'fileName' => 'a'],
            ['pathName' => $path . '/a/a-1/a-1.txt', 'pathName2' => $path . '/a/a-1/a-1.txt', 'fileName' => 'a-1.txt'],
            ['pathName' => $path . '/b/b.php', 'pathName2' => $path . '/b/b.php', 'fileName' => 'b.php'],
            ['pathName' => $path . '/b', 'pathName2' => $path . '/b', 'fileName' => 'b'],
        ];

        $files = [];
        foreach (File::enumAll($path) as $file)
        {
            $files[] = [
                'pathName'  => $file->getPathName(),
                'pathName2' => (string) $file,
                'fileName'  => $file->getFileName(),
            ];
        }
        $this->assertEqualsCanonicalizing($expectedFiles, $files);
    }

    /**
     * @testdox enumPHPFile
     */
    public function testEnumPHPFile(): void
    {
        $path = \dirname(__DIR__, 2) . '/Util/File';
        $expectedFiles = [
            $path . '/2.php',
            $path . '/b/b.php',
        ];

        $files = [];
        foreach (File::enumPHPFile($path) as $file)
        {
            $files[] = $file[0];
        }
        $this->assertEqualsCanonicalizing($expectedFiles, $files);
    }

    /**
     * @testdox enumFile
     */
    public function testEnumFile(): void
    {
        $path = \dirname(__DIR__, 2) . '/Util/File';
        $expectedFiles = [
            ['pathName' => $path . '/1.txt', 'pathName2' => $path . '/1.txt', 'fileName' => '1.txt'],
            ['pathName' => $path . '/2.php', 'pathName2' => $path . '/2.php', 'fileName' => '2.php'],
            ['pathName' => $path . '/a/a-1', 'pathName2' => $path . '/a/a-1', 'fileName' => 'a-1'],
            ['pathName' => $path . '/a', 'pathName2' => $path . '/a', 'fileName' => 'a'],
            ['pathName' => $path . '/a/a-1/a-1.txt', 'pathName2' => $path . '/a/a-1/a-1.txt', 'fileName' => 'a-1.txt'],
            ['pathName' => $path . '/b/b.php', 'pathName2' => $path . '/b/b.php', 'fileName' => 'b.php'],
            ['pathName' => $path . '/b', 'pathName2' => $path . '/b', 'fileName' => 'b'],
        ];

        $files = [];
        foreach (File::enumFile($path) as $file)
        {
            $files[] = [
                'pathName'  => $file->getFullPath(),
                'pathName2' => (string) $file,
                'fileName'  => $file->getFileName(),
            ];
        }
        $this->assertEqualsCanonicalizing($expectedFiles, $files);
    }

    /**
     * @testdox path
     */
    public function testPath(): void
    {
        $path = 'http://www.baidu.com/a/b.jpg';
        $this->assertEquals($path, File::path('http://www.baidu.com', 'a', 'b.jpg'));
        $this->assertEquals($path, File::path('http://www.baidu.com/', 'a', 'b.jpg'));
        $this->assertEquals($path, File::path('http://www.baidu.com/a', 'b.jpg'));
        $this->assertEquals($path, File::path('http://www.baidu.com/', 'a/b.jpg'));
        $this->assertEquals($path, File::path('http://www.baidu.com/', '/a/b.jpg'));

        $path = '/tmp/1/2/3.tmp';
        $this->assertEquals($path, File::path('/tmp', '1', '2/3.tmp'));
        $this->assertEquals($path, File::path('/tmp/', '1', '2/3.tmp'));
        $this->assertEquals($path, File::path('/tmp/1', '2/3.tmp'));
        $this->assertEquals($path, File::path('/tmp/', '1/2/3.tmp'));
        $this->assertEquals($path, File::path('/tmp/', '/1/2/3.tmp'));
    }

    /**
     * @testdox readAll
     */
    public function testReadAll(): void
    {
        $fp = fopen(\dirname(__DIR__, 2) . '/Util/File/1.txt', 'r');
        $this->assertIsResource($fp);
        $content = File::readAll($fp);
        fclose($fp);
        $this->assertEquals(4099, \strlen($content));
    }

    /**
     * @testdox createDir
     */
    public function testCreateDir(): void
    {
        $path = Imi::getRuntimePath('test/a/b');
        $this->assertDirectoryDoesNotExist($path);
        $this->assertTrue(File::createDir($path));
        $this->assertDirectoryExists($path);
    }

    /**
     * @testdox createFile
     */
    public function testCrateFile(): void
    {
        $path = Imi::getRuntimePath('test/test.txt');
        $this->assertFalse(is_file($path));
        $this->assertTrue(File::createFile($path));
        $this->assertTrue(is_file($path));

        $path = Imi::getRuntimePath('test/test/test.txt');
        $this->assertFalse(is_file($path));
        $this->assertTrue(File::createFile($path));
        $this->assertTrue(is_file($path));
    }

    /**
     * @testdox isEmptyDir
     */
    public function testIsEmptyDir(): void
    {
        $path = Imi::getRuntimePath('test/a/b');
        $this->assertTrue(File::isEmptyDir($path));

        $path = Imi::getRuntimePath('test');
        $this->assertFalse(File::isEmptyDir($path));
    }

    /**
     * @testdox deleteDir
     */
    public function testDeleteDir(): void
    {
        $path = Imi::getRuntimePath('test/test');
        $this->assertDirectoryExists($path);
        File::deleteDir($path);
        $this->assertDirectoryDoesNotExist($path);
    }

    /**
     * @testdox putContents
     */
    public function testPutContents(): void
    {
        $path = Imi::getRuntimePath('test/test');
        $this->assertDirectoryDoesNotExist($path);
        $content = uniqid();
        $fileName = $path . '/a/b/c/1.txt';
        $this->assertTrue(false !== File::putContents($fileName, $content));
        $this->assertEquals($content, file_get_contents($fileName));
        File::deleteDir($path . '/a/b/c');
    }

    /**
     * @testdox absolute
     */
    public function testAbsolute(): void
    {
        $this->assertEquals('/a/b/c/1.jpg', File::absolute('/a/b/d/e/../../c/./1.jpg'));
    }
}
