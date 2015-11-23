<?php

namespace Illuminati\OrderBundle\Tests\Service;

use Illuminati\OrderBundle\Services\HostOrderJoinChecker;

class HostOrderJoinCheckerTest extends \PHPUnit_Framework_TestCase
{
    public function testUserCanJoinOrder()
    {
        $tokenStorage = $this->getTokenStorageMock(true);
        $em = $this->getEntityManagerMock();

        $hostOrderJoinChecker = new HostOrderJoinChecker($em['em'], $tokenStorage);

        $this->assertEquals($em['hostOrderMock'], $hostOrderJoinChecker->check(69));
    }

    public function testInvalidOrderToken()
    {
        $tokenStorage = $this->getTokenStorageMock(false);
        $em = $this->getEntityManagerMock(true);

        $hostOrderJoinChecker = new HostOrderJoinChecker($em['em'], $tokenStorage);

        $this->assertFalse($hostOrderJoinChecker->check(69));
    }

    public function testUserAlreadyParticipates()
    {
        $tokenStorage = $this->getTokenStorageMock(true);

        $em = $this->getEntityManagerMock(false,false);

        $hostOrderJoinChecker = new HostOrderJoinChecker($em['em'], $tokenStorage);

        $this->assertGreaterThan(0, $hostOrderJoinChecker->check(69));
    }


    public function getTokenStorageMock($useMockBuilder = true)
    {
        if ($useMockBuilder) {
            $token = $this
                ->getMockBuilder('\Symfony\Component\Security\Core\Authentication\Token')
                ->disableOriginalConstructor()
                ->setMethods(['getUser'])
                ->getMock();

            $token
                ->expects($this->once())
                ->method('getUser');

            $tokenStorageMock = $this
                ->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage')
                ->disableOriginalConstructor()
                ->setMethods(['getToken'])
                ->getMock();

            $tokenStorageMock
                ->expects($this->once())
                ->method('getToken')
                ->willReturn($token);
            return $tokenStorageMock;
        }

        return $this->getMock('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage');

    }

    public function getEntityManagerMock(
        $hostOrderRepoMockNull = false, $userOrderRepoMockNull = true
    ) {
        $hostOrderMock = $this
            ->getMockBuilder('Illuminati\OrderBundle\Entity\Host_order')
            ->disableOriginalConstructor()
            ->setMethods(['getId'])
            ->getMock();

        $hostOrderMock
            ->method('getId')
            ->willReturn(1);


        $hostOrderRepositoryMock = $this
            ->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(['findOneBy'])
            ->getMock();

        $userOrderRepositoryMock = $this
            ->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(['findOneBy'])
            ->getMock();

        if ($hostOrderRepoMockNull === false) {

            $hostOrderRepositoryMock
                ->expects($this->once())
                ->method('findOneBy')
                ->willReturn($hostOrderMock);

            if ($userOrderRepoMockNull === false) {
                $userOrderRepositoryMock
                    ->expects($this->once())
                    ->method('findOneBy')
                    ->willReturn(1);
            } else {
                $userOrderRepositoryMock
                    ->expects($this->once())
                    ->method('findOneBy')
                    ->willReturn(null);
            }

        } else {
            $hostOrderRepositoryMock
                ->expects($this->once())
                ->method('findOneBy')
                ->willReturn(null);

        }

        $map = [
            ['IlluminatiOrderBundle:Host_order',$hostOrderRepositoryMock],
            ['IlluminatiOrderBundle:User_order',$userOrderRepositoryMock]
        ];

        $emMock = $this
            ->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods(['getRepository'])
            ->getMock();

        // Host order fetch

        if ($hostOrderRepoMockNull === false) {
            $emMock
                ->expects($this->exactly(2))
                ->method('getRepository')
                ->will($this->returnValueMap($map));
        } else {
            $emMock
                ->expects($this->once())
                ->method('getRepository')
                ->will($this->returnValueMap($map));
        }

        return [
            'em' => $emMock,
            'hostOrderMock' => $hostOrderMock
        ];
    }


}