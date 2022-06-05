<?php

namespace App\Tests\Service\Directory;

use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;
use App\Service\Directory\Directory;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authenticator\Token\JWTPostAuthenticationToken;
use App\Entity\User;
use App\Entity\Branch;

class DirectoryTest extends TestCase
{
    public function testDirectory()
    {
        vfsStream::setup('/');
        $path = vfsStream::url('/root');

        $branch = $this->getMockBuilder(Branch::class)
            ->disableOriginalConstructor()
            ->getMock();
        $branch->method('getId')->willReturn(1);

        $user = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->getMock();
        $user->method('getBranch')->willReturn($branch);

        $token = $this->getMockBuilder(JWTPostAuthenticationToken::class)
            ->disableOriginalConstructor()
            ->getMock();
        $token->method('getUser')->willReturn($user);

        $tokenStorage = $this->getMockBuilder(TokenStorageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $tokenStorage->method('getToken')->willReturn($token);

        $directory = new Directory($tokenStorage);
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

        // upload file
        $file = $this->getMockBuilder(File::class)
            ->disableOriginalConstructor()
            ->getMock();

        $uploadedFile = $this->getMockBuilder(UploadedFile::class)
            ->disableOriginalConstructor()
            ->getMock();
        $uploadedFile->method('getSize')->willReturn(1);
        $uploadedFile->method('getMimeType')->willReturn('image/jpg');
        $uploadedFile->method('move')->willReturn($file);

        $result = $directory->upload($uploadedFile, 'file_upload.jpg', 'test');
        $this->assertTrue($result instanceof File);

        // rename file
        $directory->touch('file_orig.jpg', 'test');
        $directory->rename('file_orig.jpg', 'file_target.jpg', 'test');
        $this->assertTrue(is_file($path . '/test/file_target.jpg'));

        // remove file
        $directory->remove('file.jpg', 'test');
        $directory->remove('file.jpg', 'test/dir');
        $directory->remove('file2.jpg', 'test');
        $directory->remove('file_upload.jpg', 'test');
        $directory->remove('file_target.jpg', 'test');
        $this->assertTrue(!is_file($path . '/test/file.jpg'));

        // list directory
        $directory->touch('file1.docx', 'test');
        $directory->touch('file2.docx', 'test');
        $dir = $directory->list('./test/');
        $this->assertIsArray($dir);
        $this->assertEquals(2, count($dir));

        // remove directory
        $directory->remove('dir', 'test');
        $directory->remove('test', './');
        $this->assertTrue(!is_dir($path . '/test'));
    }
}
