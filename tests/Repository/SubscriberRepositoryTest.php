<?php

namespace App\Tests\Repository;

use App\Entity\Subscriber;
use App\Repository\SubscriberRepository;
use App\Tests\AbstractRepositoryTest;

class SubscriberRepositoryTest extends AbstractRepositoryTest
{
    private SubscriberRepository $subscriberRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subscriberRepository = $this->getRepositoryForEntity(Subscriber::class);
    }

    public function testRemove(): void
    {
        $email      = 'testRemove@mail.com';
        $subscriber =  (new Subscriber())->setEmail($email);

        $this->em->persist($subscriber);
        $this->em->flush();

        $this->assertNotNull($this->subscriberRepository->findOneBy(['email' => $email]));

        $this->subscriberRepository->remove($subscriber, true);

        $this->assertNull($this->subscriberRepository->findOneBy(['email' => $email]));
    }

    public function testExistByEmail(): void
    {
        $email = 'testExistByEmail@mail.com';

        $this->em->persist((new Subscriber())->setEmail($email));
        $this->em->flush();

        $this->assertTrue($this->subscriberRepository->existByEmail($email));
        $this->assertFalse($this->subscriberRepository->existByEmail('NoExistEmail@mail.com'));
    }
}
