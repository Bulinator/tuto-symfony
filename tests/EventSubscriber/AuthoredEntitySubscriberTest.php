<?php

namespace App\Tests\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\BlogPost;
use App\Entity\User;
use App\EventSubscriber\AuthoredEntitySubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class AuthoredEntitySubscriberTest extends TestCase
{
    public function testConfiguration()
    {
        $result = AuthoredEntitySubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(KernelEvents::VIEW, $result);
        $this->assertEquals(
            ['getAuthenticatedUser', EventPriorities::PRE_WRITE],
            $result[KernelEvents::VIEW]
        );
    }

    public function testSetAuthorCall()
    {
        $entityMock = $this->getEntityMock(BlogPost::class, true);

        $tokenStorageMock = $this->getTokenStorageMock();
        $eventMock = $this->getEventMock('POST', $entityMock);
        (new AuthoredEntitySubscriber($tokenStorageMock))->getAuthenticatedUser($eventMock);

        $entityMock = $this->getEntityMock('Nonexisting', false);

        $tokenStorageMock = $this->getTokenStorageMock();
        $eventMock = $this->getEventMock('GET', $entityMock);
        (new AuthoredEntitySubscriber($tokenStorageMock))->getAuthenticatedUser($eventMock);
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function getTokenStorageMock(): \PHPUnit\Framework\MockObject\MockObject
    {
        $tokenMock = $this->getMockBuilder(TokenInterface::class)
            ->getMockForAbstractClass();
        $tokenMock->expects($this->exactly(1))
            ->method('getUser')
            ->willReturn(new User());

        $tokenStorageMock = $this->getMockBuilder(TokenStorageInterface::class)->getMockForAbstractClass();
        $tokenStorageMock->expects($this->once())
            ->method('getToken')
            ->willReturn($tokenMock);
        return $tokenStorageMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function getEventMock(): \PHPUnit\Framework\MockObject\MockObject
    {
        $requestMock = $this->getMockBuilder(Request::class)->getMock();
        $requestMock->expects($this->once())
            ->method('getMethod')
            ->willReturn('POST');

        $eventMock = $this->getMockBuilder(ViewEvent::class)
            ->disableOriginalConstructor()
            ->getMock();

        $eventMock->expects($this->once())
            ->method('getControllerResult')
            ->willReturn(new BlogPost());

        $eventMock->expects($this->once())
            ->method('getRequest')
            ->willReturn($requestMock);

        return $eventMock;
    }

    /**
     * @param $className
     * @param $shouldCallSetAuthor
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function getEntityMock($className, $shouldCallSetAuthor): \PHPUnit\Framework\MockObject\MockObject
    {
        $entityMock = $this->getMockBuilder($className)
            ->setMethods(['setAuthor'])
            ->getMock();
        $entityMock->expects($shouldCallSetAuthor ? $$this->once() : $this->never())
            ->method('setAuthor');
        return $entityMock;
    }
}