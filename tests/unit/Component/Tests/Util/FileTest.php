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
     * @testdox path
     */
    public function testPath(): void
    {
        $this->assertEquals('', File::path());
        $path = 'http://www.baidu.com/a/b.jpg';
        $this->assertEquals($path, File::path('http://www.baidu.com', 'a', 'b.jpg'));
        $this->assertEquals($path, File::path('http://www.baidu.com/', 'a', 'b.jpg'));
        $this->assertEquals($path, File::path('http://www.baidu.com/a', 'b.jpg'));
        $this->assertEquals($path, File::path('http://www.baidu.com/', 'a/b.jpg'));
        $this->assertEquals($path, File::path('http://www.baidu.com/', '/a/b.jpg'));

        if ('\\' === \DIRECTORY_SEPARATOR)
        {
            $path = 'C:\Windows\Temp\1\2\3.tmp';
            $this->assertEquals($path, File::path('C:\Windows\Temp', '1', '2\3.tmp'));
            $this->assertEquals($path, File::path('C:\Windows\Temp\\', '1', '2\3.tmp'));
            $this->assertEquals($path, File::path('C:\Windows\Temp\\1', '2\3.tmp'));
            $this->assertEquals($path, File::path('C:\Windows\Temp\\', '1\2\3.tmp'));
            $this->assertEquals($path, File::path('C:\Windows\Temp\\', '\1\2\3.tmp'));
        }
        else
        {
            $path = '/tmp/1/2/3.tmp';
            $this->assertEquals($path, File::path('/tmp', '1', '2/3.tmp'));
            $this->assertEquals($path, File::path('/tmp/', '1', '2/3.tmp'));
            $this->assertEquals($path, File::path('/tmp/1', '2/3.tmp'));
            $this->assertEquals($path, File::path('/tmp/', '1/2/3.tmp'));
            $this->assertEquals($path, File::path('/tmp/', '/1/2/3.tmp'));
        }
    }

    /**
     * @testdox enum
     */
    public function testEnum(): void
    {
        $this->assertEquals([], File::enum('not file'));

        $path = File::path(\dirname(__DIR__, 2) . '/Util/File');
        $expectedFiles = [
            ['pathName' => File::path($path, '1.txt'), 'pathName2' => File::path($path, '1.txt'), 'fileName' => '1.txt'],
            ['pathName' => File::path($path, '2.php'), 'pathName2' => File::path($path, '2.php'), 'fileName' => '2.php'],
            ['pathName' => File::path($path, 'a', 'a-1', 'a-1.txt'), 'pathName2' => File::path($path, 'a', 'a-1', 'a-1.txt'), 'fileName' => 'a-1.txt'],
            ['pathName' => File::path($path, 'b', 'b.php'), 'pathName2' => File::path($path, 'b', 'b.php'), 'fileName' => 'b.php'],
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
        $result = File::enumAll('not file');
        $this->assertEquals(0, iterator_count($result));

        $path = \dirname(__DIR__, 2) . '/Util/File';
        $expectedFiles = [
            ['pathName' => File::path($path, '1.txt'), 'pathName2' => File::path($path, '1.txt'), 'fileName' => '1.txt'],
            ['pathName' => File::path($path, '2.php'), 'pathName2' => File::path($path, '2.php'), 'fileName' => '2.php'],
            ['pathName' => File::path($path, 'a', 'a-1'), 'pathName2' => File::path($path, 'a', 'a-1'), 'fileName' => 'a-1'],
            ['pathName' => File::path($path, 'a'), 'pathName2' => File::path($path, 'a'), 'fileName' => 'a'],
            ['pathName' => File::path($path, 'a', 'a-1', 'a-1.txt'), 'pathName2' => File::path($path, 'a', 'a-1', 'a-1.txt'), 'fileName' => 'a-1.txt'],
            ['pathName' => File::path($path, 'b', 'b.php'), 'pathName2' => File::path($path, 'b', 'b.php'), 'fileName' => 'b.php'],
            ['pathName' => File::path($path, 'b'), 'pathName2' => File::path($path, 'b'), 'fileName' => 'b'],
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
        $result = File::enumPHPFile('not file');
        $this->assertEquals(0, iterator_count($result));

        $path = \dirname(__DIR__, 2) . '/Util/File';
        $expectedFiles = [
            File::path($path, '2.php'),
            File::path($path, 'b', 'b.php'),
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
        $result = File::enumFile('not file');
        $this->assertFalse($result->getReturn());

        $path = \dirname(__DIR__, 2) . '/Util/File';
        $expectedFiles = [
            ['path' => $path, 'pathName' => File::path($path, '1.txt'), 'pathName2' => File::path($path, '1.txt'), 'fileName' => '1.txt'],
            ['path' => $path, 'pathName' => File::path($path, '2.php'), 'pathName2' => File::path($path, '2.php'), 'fileName' => '2.php'],
            ['path' => $path . \DIRECTORY_SEPARATOR . 'a', 'pathName' => File::path($path, 'a', 'a-1'), 'pathName2' => File::path($path, 'a', 'a-1'), 'fileName' => 'a-1'],
            ['path' => $path, 'pathName' => File::path($path, 'a'), 'pathName2' => File::path($path, 'a'), 'fileName' => 'a'],
            ['path' => $path . \DIRECTORY_SEPARATOR . 'a' . \DIRECTORY_SEPARATOR . 'a-1', 'pathName' => File::path($path, 'a', 'a-1', 'a-1.txt'), 'pathName2' => File::path($path, 'a', 'a-1', 'a-1.txt'), 'fileName' => 'a-1.txt'],
            ['path' => $path . \DIRECTORY_SEPARATOR . 'b', 'pathName' => File::path($path, 'b', 'b.php'), 'pathName2' => File::path($path, 'b', 'b.php'), 'fileName' => 'b.php'],
            ['path' => $path, 'pathName' => File::path($path, 'b'), 'pathName2' => File::path($path, 'b'), 'fileName' => 'b'],
        ];

        $files = [];
        foreach (File::enumFile($path) as $file)
        {
            $files[] = [
                'path'      => $file->getPath(),
                'pathName'  => $file->getFullPath(),
                'pathName2' => (string) $file,
                'fileName'  => $file->getFileName(),
            ];
        }
        $this->assertEqualsCanonicalizing($expectedFiles, $files);

        $files = [];
        foreach (File::enumFile($path) as $file)
        {
            $files[] = [
                'path'      => $file->getPath(),
                'pathName'  => $file->getFullPath(),
                'pathName2' => (string) $file,
                'fileName'  => $file->getFileName(),
            ];
            if (is_dir($file->getFullPath()))
            {
                $file->setContinue(false);
            }
        }
        $this->assertEqualsCanonicalizing([$expectedFiles[0], $expectedFiles[1], $expectedFiles[3], $expectedFiles[6]], $files);
    }

    /**
     * @testdox enumFileSync
     */
    public function testEnumFileSync(): void
    {
        $result = File::enumFileSync('not file');
        $this->assertFalse($result->getReturn());

        $path = \dirname(__DIR__, 2) . '/Util/File';
        $expectedFiles = [
            ['pathName' => File::path($path, '1.txt'), 'pathName2' => File::path($path, '1.txt'), 'fileName' => '1.txt'],
            ['pathName' => File::path($path, '2.php'), 'pathName2' => File::path($path, '2.php'), 'fileName' => '2.php'],
            ['pathName' => File::path($path, 'a', 'a-1'), 'pathName2' => File::path($path, 'a', 'a-1'), 'fileName' => 'a-1'],
            ['pathName' => File::path($path, 'a'), 'pathName2' => File::path($path, 'a'), 'fileName' => 'a'],
            ['pathName' => File::path($path, 'a', 'a-1', 'a-1.txt'), 'pathName2' => File::path($path, 'a', 'a-1', 'a-1.txt'), 'fileName' => 'a-1.txt'],
            ['pathName' => File::path($path, 'b', 'b.php'), 'pathName2' => File::path($path, 'b', 'b.php'), 'fileName' => 'b.php'],
            ['pathName' => File::path($path, 'b'), 'pathName2' => File::path($path, 'b'), 'fileName' => 'b'],
        ];

        $files = [];
        foreach (File::enumFileSync($path) as $file)
        {
            $files[] = [
                'pathName'  => $file->getFullPath(),
                'pathName2' => (string) $file,
                'fileName'  => $file->getFileName(),
            ];
        }
        $this->assertEqualsCanonicalizing($expectedFiles, $files);

        $files = [];
        foreach (File::enumFileSync($path, '/1\.txt/') as $file)
        {
            $files[] = $file->getFullPath();
        }
        $this->assertEqualsCanonicalizing([
            File::path($path, '1.txt'),
        ], $files);
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
        $this->assertFalse(File::createDir(''));
    }

    /**
     * @testdox createFile
     */
    public function testCrateFile(): void
    {
        $this->assertFalse(File::createFile(''));

        $path = Imi::getRuntimePath('test/test.txt');
        $this->assertFalse(is_file($path));
        $this->assertTrue(File::createFile($path));
        $this->assertTrue(is_file($path));
        $this->assertTrue(File::createFile($path)); // 已存在时的测试

        $path = Imi::getRuntimePath('test/test/test.txt');
        $this->assertFalse(is_file($path));
        $this->assertTrue(File::createFile($path, '123'));
        $this->assertTrue(is_file($path));
        $this->assertEquals('123', file_get_contents($path));
    }

    /**
     * @testdox isEmptyDir
     */
    public function testIsEmptyDir(): void
    {
        $path = Imi::getRuntimePath('test/not found');
        $this->assertTrue(File::isEmptyDir($path));

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
        mkdir(Imi::getRuntimePath('test/test/a'));
        file_put_contents(Imi::getRuntimePath('test/test/a/1.txt'), '123');
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
        if ('\\' === \DIRECTORY_SEPARATOR)
        {
            $this->assertEquals('\a\b\c\1.jpg', File::absolute('/a/b/d/e/../../c/./1.jpg'));
            $this->assertEquals('phar://\a.phar\b\c\1.jpg', File::absolute('phar:///a.phar/b/d/e/../../c/./1.jpg'));
        }
        else
        {
            $this->assertEquals('/a/b/c/1.jpg', File::absolute('/a/b/d/e/../../c/./1.jpg'));
            $this->assertEquals('phar:///a.phar/b/c/1.jpg', File::absolute('phar:///a.phar/b/d/e/../../c/./1.jpg'));
        }
    }

    public function testGetBaseNameBeforeFirstDot(): void
    {
        $this->assertEquals('', File::getBaseNameBeforeFirstDot('abc'));
        $this->assertEquals('abc', File::getBaseNameBeforeFirstDot('abc.php'));
        $this->assertEquals('abc', File::getBaseNameBeforeFirstDot('abc.php.bak'));
        $this->assertEquals('abc', File::getBaseNameBeforeFirstDot(__DIR__ . \DIRECTORY_SEPARATOR . 'abc.php'));
        $this->assertEquals('abc', File::getBaseNameBeforeFirstDot(__DIR__ . \DIRECTORY_SEPARATOR . 'abc.php.bak'));
    }
}
