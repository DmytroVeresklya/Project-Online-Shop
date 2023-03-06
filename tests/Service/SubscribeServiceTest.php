<?php

namespace App\Tests\Service;

use App\Entity\Subscriber;
use App\Exception\SubscriberAlreadyExistException;
use App\Model\SubscribeRequest;
use App\Repository\SubscriberRepository;
use App\Service\SubscribeService;
use App\Tests\AbstractTestCase;

class SubscribeServiceTest extends AbstractTestCase
{
    private SubscriberRepository $subscriberRepository;

    private const EMAIL = "test@test.com";

    protected function setUp(): void
    {
        parent::setUp();

        $this->subscriberRepository = $this->createMock(SubscriberRepository::class);
    }

    public function testSubscribeAlreadyExist()
    {
        $this->expectException(SubscriberAlreadyExistException::class);

        $this->subscriberRepository->expects($this->once())
            ->method('existByEmail')
            ->with(self::EMAIL)
            ->willReturn(true);

        $request = new SubscribeRequest();
        $request->setEmail(self::EMAIL);

        (new SubscribeService($this->subscriberRepository))->subscribe($request);
    }

    public function testSubscribeSuccess()
    {
        $this->subscriberRepository->expects($this->once())
            ->method('existByEmail')
            ->with(self::EMAIL)
            ->willReturn(false);

        $expectedSubscriber = new Subscriber();
        $expectedSubscriber->setEmail(self::EMAIL);

        $this->subscriberRepository->expects($this->once())
            ->method('save')
            ->with($expectedSubscriber);

        $request = new SubscribeRequest();
        $request->setEmail(self::EMAIL);

        (new SubscribeService($this->subscriberRepository))->subscribe($request);
    }
}
