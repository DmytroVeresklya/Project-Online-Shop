<?php

namespace App\Tests\EventListener;

use App\EventListener\JwtCreatedListener;
use App\Tests\AbstractTestCase;
use App\Tests\MockUtils;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JwtCreatedListenerTest extends AbstractTestCase
{
    public function testInvoke(): void
    {
        $user = MockUtils::createUser();
        $this->setEntityId($user, 12);

        $listener = new JwtCreatedListener();
        $event = new JWTCreatedEvent(['flag' => true], $user, []);

        $listener($event);

        $this->assertEquals(['flag' => true, 'id' => 12], $event->getData());
    }
}
