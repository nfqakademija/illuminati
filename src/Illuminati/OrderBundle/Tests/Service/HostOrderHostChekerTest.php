<?php

namespace Illuminati\OrderBundle\Tests\Service;

use Illuminati\OrderBundle\Services\HostOrderHostChecker;

class HostOrderHostChekerTest extends \PHPUnit_Framework_TestCase
{
    public function testUserIsHostOfGroupOrder()
    {
        $mocks = $this->mockPreparations();

        $hostOrderHostChecker = new HostOrderHostChecker($mocks['em'], $mocks['ts']);

        $this->assertEquals($mocks['hostOrder'], $hostOrderHostChecker->check(69));
    }

    public function testUserIsNotHostOfGroupOrder()
    {
        $mocks = $this->mockPreparations(true);

        $hostOrderHostChecker = new HostOrderHostChecker($mocks['em'], $mocks['ts']);

        $this->assertFalse($hostOrderHostChecker->check(69));
    }

    public function testNoHostOrderFound()
    {
        $repositoryMock = $this
            ->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(['find'])
            ->getMock();

        $repositoryMock
            ->expects($this->once())
            ->method('find')
            ->willReturn(null);

        $entityManagerMock = $this
            ->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods(['getRepository'])
            ->getMock();

        $entityManagerMock->expects($this->once())
            ->method('getRepository')
            ->willReturn($repositoryMock);

        $tokenStorageMock = $this
            ->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage')
            ->disableOriginalConstructor()
            ->getMock();

        $hostOrderHostChecker = new HostOrderHostChecker(
            $entityManagerMock, $tokenStorageMock
        );

        $this->assertFalse($hostOrderHostChecker->check(69));
    }

    public function testInvalidArgumentException()
    {
        $em = $this
            ->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();
        $ts = $this
            ->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage')
            ->disableOriginalConstructor()
            ->getMock();

        $this->setExpectedException('InvalidArgumentException');

        $hostOrderHostChecker = new HostOrderHostChecker($em, $ts);

        $hostOrderHostChecker->check("69");
    }


    public function mockPreparations($differentUserMocks = false)
    {
        $userEntityMock = $this->getMock('Illuminati\UserBundle\Entity\User');

        // Host Order Mock

        $hostOrderMock = $this
            ->getMockBuilder('Illuminati\OrderBundle\Entity\Host_order')
            ->disableOriginalConstructor()
            ->setMethods(['getUsersId'])
            ->getMock();

        $hostOrderMock
            ->expects($this->once())
            ->method('getUsersId')
            ->willReturn($userEntityMock);

        // Repository mock
        $repositoryMock = $this
            ->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(['find'])
            ->getMock();

        $repositoryMock
            ->expects($this->once())
            ->method('find')
            ->willReturn($hostOrderMock);

        // Entity manager mock
        $entityManagerMock = $this
            ->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods(['getRepository'])
            ->getMock();

        $entityManagerMock
            ->expects($this->once())
            ->method('getRepository')
            ->with('IlluminatiOrderBundle:Host_order')
            ->willReturn($repositoryMock);

        // Token storage mock

        $token = $this
            ->getMockBuilder('\Symfony\Component\Security\Core\Authentication\Token')
            ->disableOriginalConstructor()
            ->setMethods(['getUser'])
            ->getMock();

        if ($differentUserMocks === true) {
            // creating a different user mock
            $userEntityMock = $this->getMock('Illuminati\UserBundle\Entity\User');
        }

        $token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($userEntityMock);

        $tokenStorageMock = $this
            ->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage')
            ->disableOriginalConstructor()
            ->setMethods(['getToken'])
            ->getMock();

        $tokenStorageMock
            ->expects($this->once())
            ->method('getToken')
            ->willReturn($token);

        return [
            'em'        => $entityManagerMock,
            'ts'        => $tokenStorageMock,
            'hostOrder' => $hostOrderMock
        ];
    }
}