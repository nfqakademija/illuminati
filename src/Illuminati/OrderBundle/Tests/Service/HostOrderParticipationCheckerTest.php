<?php

namespace Illuminati\OrderBundle\Tests\Service;

use Illuminati\OrderBundle\Services\HostOrderParticipationChecker;


class HostOrderParticipationCheckerTest extends \PHPUnit_Framework_TestCase
{

    public function testExistingParticipant()
    {
        $mocks = $this->mockPreperations();

        $em = new HostOrderParticipationChecker(
            $mocks['em'], $mocks['ts']
        );

        $this->assertEquals($mocks['hostOrderMock'], $em->check(69));
    }


    public function testNonExistingParticipant()
    {
        $mocks = $this->mockPreperations(true);

        $em = new HostOrderParticipationChecker(
            $mocks['em'], $mocks['ts']
        );

        $this->assertFalse($em->check(69));
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

        $em = new HostOrderParticipationChecker(
            $entityManagerMock, $tokenStorageMock
        );

        $this->assertFalse($em->check(69));
    }

    public function testInvalidArgumentException()
    {

        $em = $this
            ->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $ts = $this
            ->getMockBuilder('\Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage')
            ->disableOriginalConstructor()
            ->getMock();

        $em = new HostOrderParticipationChecker(
            $em, $ts
        );
        $this->setExpectedException('InvalidArgumentException');

        $em->check("69");
    }


    public function mockPreperations($differentUserMocks = false)
    {
        $userEntityMock = $this->getMock('Illuminati\UserBundle\Entity\User');

        $userOrderMock = $this
            ->getMockBuilder('Illuminati\OrderBundle\Entity\User_order')
            ->disableOriginalConstructor()
            ->setMethods(['getUsersId'])
            ->getMock();

        $userOrderMock
            ->expects($this->once())
            ->method('getUsersId')
            ->will($this->returnValue($userEntityMock));

        $userOrderArrayMock = $this
            ->getMockBuilder('Illuminati\OrderBundle\Entity\User_order')
            ->disableOriginalConstructor()
            ->setMethods(['getValues'])
            ->getMock();

        $userOrderArrayMock
            ->expects($this->once())
            ->method('getValues')
            ->will($this->returnValue([0 => $userOrderMock]));

        // Host order mock

        $hostOrderMock = $this
            ->getMockBuilder('Illuminati\OrderBundle\Entity\Host_order')
            ->setMethods(['getUserOrders'])
            ->disableOriginalConstructor()
            ->getMock();

        $hostOrderMock
            ->expects($this->once())
            ->method('getUserOrders')
            ->will($this->returnValue($userOrderArrayMock));

        // Entity manager mocks

        $repositoryMock = $this
            ->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(['find'])
            ->getMock();

        $repositoryMock
            ->expects($this->once())
            ->method('find')
            ->will($this->returnValue($hostOrderMock));

        $entityManagerMock = $this
            ->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->setMethods(['getRepository'])
            ->disableOriginalConstructor()
            ->getMock();

        $entityManagerMock
            ->expects($this->once())
            ->method('getRepository')
            ->with('IlluminatiOrderBundle:Host_order')
            ->will($this->returnValue($repositoryMock));

        // Token storage mock

        $token = $this
            ->getMockBuilder('\Symfony\Component\Security\Core\Authentication\Token')
            ->disableOriginalConstructor()
            ->setMethods(['getUser'])
            ->getMock();

        if ($differentUserMocks == true) {

            // creating different user entity mock for the token storage
            $userEntityMock = $this->getMock('Illuminati\UserBundle\Entity\User');
        }

        $token->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue($userEntityMock));

        $tokenStorageMock = $this
            ->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage')
            ->disableOriginalConstructor()
            ->setMethods(['getToken'])
            ->getMock();

        $tokenStorageMock
            ->expects($this->once())
            ->method('getToken')
            ->will($this->returnValue($token));

        return [
            'em'=>$entityManagerMock,
            'ts'=>$tokenStorageMock,
            'hostOrderMock'=> $hostOrderMock
        ];
    }


}