<?php

namespace App\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Throwable;

abstract class AbstractTestCase extends TestCase
{
    protected function setEntityId(object $entity, int $value, $idField = 'id')
    {
        $class = new ReflectionClass($entity);

        $property = $class->getProperty($idField);
        $property->setAccessible(true);
        $property->setValue($entity, $value);
        $property->setAccessible(false);
    }

    protected function assertResponse(int $exceptedStatusCode, string $exceptedBody, Response $actualResponse): void
    {
        $this->assertEquals($exceptedStatusCode, $actualResponse->getStatusCode());
        $this->assertInstanceOf(JsonResponse::class, $actualResponse);
        $this->assertJsonStringEqualsJsonString($exceptedBody, $actualResponse->getContent());
    }

    protected function createExceptionEvent(Throwable $exception): ExceptionEvent
    {
        return new ExceptionEvent(
            $this->createTestKernel(),
            new Request(),
            HttpKernelInterface::MAIN_REQUEST,
            $exception
        );
    }

    private function createTestKernel(): HttpKernelInterface
    {
        return new class () implements HttpKernelInterface {
            public function handle(Request $request, int $type = self::MAIN_REQUEST, bool $catch = true): Response
            {
                return new Response('test', Response::HTTP_OK);
            }
        };
    }
}
