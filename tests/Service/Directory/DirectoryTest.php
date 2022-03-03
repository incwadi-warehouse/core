<?php

namespace App\Tests\Service\Directory;

use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;
use App\Service\Directory\Directory;

class DirectoryTest extends TestCase
{
    public function testDirectory()
    {
        vfsStream::setup('/');
        $path = vfsStream::url('/root');

        $directory = new Directory();
        $directory->setBasePath($path);

        // create directory
        $directory->mkdir('test', './');
        $this->assertTrue(is_dir($path . '/test'));

        $directory->mkdir('dir', 'test');
        $this->assertTrue(is_dir($path . '/test/dir'));

        $directory->mkdir('dir2', 'test/dir/../');
        $this->assertTrue(is_dir($path . '/test/dir2'));

        // create file
        $directory->touch('file.jpg', 'test');
        $this->assertTrue(is_file($path . '/test/file.jpg'));

        $directory->touch('file.jpg', 'test/dir');
        $this->assertTrue(is_file($path . '/test/dir/file.jpg'));

        $directory->touch('file2.jpg', 'test/dir/../');
        $this->assertTrue(is_file($path . '/test/file2.jpg'));

        $directory->touch('file:.jpg', '/test');
        $this->assertFalse(is_file($path . '/test/file:.jpg'));

        // list directory
        $dir = $directory->list('./');
        $this->assertIsArray($dir);
        $this->assertEquals(2, count($dir));

        // remove file
        $directory->remove('file.jpg', 'test');
        $directory->remove('file.jpg', 'test/dir');
        $directory->remove('file2.jpg', 'test');
        $this->assertTrue(!is_file($path . '/test/file.jpg'));

        // remove directory
        $directory->remove('dir', 'test');
        $directory->remove('test', './');
        $this->assertTrue(!is_dir($path . '/test'));
    }
}
