<?php

namespace App\Tests\EventListener;

use App\EventListener\ApiExceptionListener;
use App\Model\ErrorResponse;
use App\Service\ExceptionHandler\ExceptionMapping;
use App\Service\ExceptionHandler\ExceptionMappingResolver;
use App\Tests\AbstractTestCase;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class ApiExceptionListenerTest extends AbstractTestCase
{
    private ExceptionMappingResolver $resolver;

    private LoggerInterface $logger;

    private SerializerInterface $serializer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resolver   = $this->createMock(ExceptionMappingResolver::class);
        $this->logger     = $this->createMock(LoggerInterface::class);
        $this->serializer = $this->createMock(SerializerInterface::class);
    }

    public function testNon500MappingWithHiddenMessage(): void
    {
        $mapping         = ExceptionMapping::fromCode(Response::HTTP_NOT_FOUND);
        $responseMessage = Response::$statusTexts[$mapping->getCode()];
        $responseBody    = json_encode(['error' => $responseMessage]);

        $this->resolver->expects($this->once())
            ->method('resolve')
            ->with(InvalidArgumentException::class)
            ->willReturn($mapping);

        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with(new ErrorResponse($responseMessage), JsonEncoder::FORMAT)
            ->willReturn($responseBody);

        $event = $this->createEvent(new InvalidArgumentException('test'));

        $this->runListener($event);

        $this->assertResponse(Response::HTTP_NOT_FOUND, $responseBody, $event->getResponse());
    }

    public function testNon500MappingWithPublicMessage(): void
    {
        $mapping         = new ExceptionMapping(Response::HTTP_NOT_FOUND, false, false);
        $responseMessage = 'test exception';
        $responseBody    = json_encode(['error' => $responseMessage]);

        $this->resolver->expects($this->once())
            ->method('resolve')
            ->with(InvalidArgumentException::class)
            ->willReturn($mapping);

        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with(new ErrorResponse($responseMessage), JsonEncoder::FORMAT)
            ->willReturn($responseBody);

        $event = $this->createEvent(new InvalidArgumentException('test exception'));

        $this->runListener($event);

        $this->assertResponse(Response::HTTP_NOT_FOUND, $responseBody, $event->getResponse());
    }

    public function testNon500LoggableMappingTriggersLogger(): void
    {
        $mapping         = new ExceptionMapping(Response::HTTP_NOT_FOUND, false, true);
        $responseMessage = 'test exception';
        $responseBody    = json_encode(['error' => $responseMessage]);

        $this->resolver->expects($this->once())
            ->method('resolve')
            ->with(InvalidArgumentException::class)
            ->willReturn($mapping);

        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with(new ErrorResponse($responseMessage), JsonEncoder::FORMAT)
            ->willReturn($responseBody);

        $this->logger->expects($this->once())
            ->method('error');

        $event = $this->createEvent(new InvalidArgumentException('test exception'));

        $this->runListener($event);

        $this->assertResponse(Response::HTTP_NOT_FOUND, $responseBody, $event->getResponse());
    }

    public function test500Loggable(): void
    {
        $mapping         = ExceptionMapping::fromCode(Response::HTTP_BAD_GATEWAY);
        $responseMessage = Response::$statusTexts[$mapping->getCode()];
        $responseBody    = json_encode(['error' => $responseMessage]);

        $this->resolver->expects($this->once())
            ->method('resolve')
            ->with(InvalidArgumentException::class)
            ->willReturn($mapping);

        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with(new ErrorResponse($responseMessage), JsonEncoder::FORMAT)
            ->willReturn($responseBody);

        $this->logger->expects($this->once())
            ->method('error')
            ->with('error message');

        $event = $this->createEvent(new InvalidArgumentException('error message'));

        $this->runListener($event);

        $this->assertResponse(Response::HTTP_BAD_GATEWAY, $responseBody, $event->getResponse());
    }

    public function test500IsDefaultThenMappingNotFound(): void
    {
        $responseMessage = Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR];
        $responseBody    = json_encode(['error' => $responseMessage]);

        $this->resolver->expects($this->once())
            ->method('resolve')
            ->with(InvalidArgumentException::class)
            ->willReturn(null);

        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with(new ErrorResponse($responseMessage), JsonEncoder::FORMAT)
            ->willReturn($responseBody);

        $this->logger->expects($this->once())
            ->method('error')
            ->with('error message');

        $event = $this->createEvent(new InvalidArgumentException('error message'));

        $this->runListener($event);

        $this->assertResponse(Response::HTTP_INTERNAL_SERVER_ERROR, $responseBody, $event->getResponse());
    }

    public function testShowTraceWhenDebug(): void
    {
        $mapping         = ExceptionMapping::fromCode(Response::HTTP_NOT_FOUND);
        $responseMessage = Response::$statusTexts[$mapping->getCode()];
        $responseBody    = json_encode(['error' => $responseMessage, 'trace' => 'something']);

        $this->resolver->expects($this->once())
            ->method('resolve')
            ->with(InvalidArgumentException::class)
            ->willReturn($mapping);

        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with(
                $this->callback(function (ErrorResponse $response) use ($responseMessage) {
                    return $response->getMessage() == $responseMessage && !empty($response->getDetails()['trace']);
                }),
                JsonEncoder::FORMAT
            )
            ->willReturn($responseBody);

        $event = $this->createEvent(new InvalidArgumentException('error message'));

        $this->runListener($event, true);

        $this->assertResponse(Response::HTTP_NOT_FOUND, $responseBody, $event->getResponse());
    }

    private function createEvent(InvalidArgumentException $exception): ExceptionEvent
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

    private function runListener(ExceptionEvent $event, bool $isDebug = false): void
    {
        (new ApiExceptionListener($this->resolver, $this->logger, $this->serializer, $isDebug))($event);
    }

    private function assertResponse(int $exceptedStatusCode, string $exceptedBody, Response $actualResponse): void
    {
        $this->assertEquals($exceptedStatusCode, $actualResponse->getStatusCode());
        $this->assertInstanceOf(JsonResponse::class, $actualResponse);
        $this->assertJsonStringEqualsJsonString($exceptedBody, $actualResponse->getContent());
    }
}
