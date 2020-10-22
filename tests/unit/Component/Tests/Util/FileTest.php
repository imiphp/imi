<?php

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
     *
     * @return void
     */
    public function testEnum()
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
     *
     * @return void
     */
    public function testEnumAll()
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
     *
     * @return void
     */
    public function testEnumPHPFile()
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
     *
     * @return void
     */
    public function testEnumFile()
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
     *
     * @return void
     */
    public function testPath()
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
     *
     * @return void
     */
    public function testReadAll()
    {
        $fp = fopen(\dirname(__DIR__, 2) . '/Util/File/1.txt', 'r');
        $this->assertIsResource($fp);
        $content = File::readAll($fp);
        fclose($fp);
        $this->assertEquals(4099, \strlen($content));
    }

    /**
     * @testdox createDir
     *
     * @return void
     */
    public function testCreateDir()
    {
        $path = Imi::getRuntimePath('test/a/b');
        $this->assertDirectoryDoesNotExist($path);
        $this->assertTrue(File::createDir($path));
        $this->assertDirectoryExists($path);
    }

    /**
     * @testdox createFile
     *
     * @return void
     */
    public function testCrateFile()
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
     *
     * @return void
     */
    public function testIsEmptyDir()
    {
        $path = Imi::getRuntimePath('test/a/b');
        $this->assertTrue(File::isEmptyDir($path));

        $path = Imi::getRuntimePath('test');
        $this->assertFalse(File::isEmptyDir($path));
    }

    /**
     * @testdox deleteDir
     *
     * @return void
     */
    public function testDeleteDir()
    {
        $path = Imi::getRuntimePath('test/test');
        $this->assertDirectoryExists($path);
        File::deleteDir($path);
        $this->assertDirectoryDoesNotExist($path);
    }

    /**
     * @testdox putContents
     *
     * @return void
     */
    public function testPutContents()
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
     *
     * @return void
     */
    public function testAbsolute()
    {
        $this->assertEquals('/a/b/c/1.jpg', File::absolute('/a/b/d/e/../../c/./1.jpg'));
    }
}
