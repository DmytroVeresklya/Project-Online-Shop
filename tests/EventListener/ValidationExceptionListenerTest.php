<?php

namespace App\Tests\EventListener;

use App\EventListener\ValidationExceptionListener;
use App\Exception\ValidationException;
use App\Model\ErrorResponse;
use App\Model\ErrorValidationsDetails;
use App\Tests\AbstractTestCase;
use Doctrine\DBAL\Schema\View;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class ValidationExceptionListenerTest extends AbstractTestCase
{
    private SerializerInterface $serializer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->serializer = $this->createMock(SerializerInterface::class);
    }

    public function testValidationExceptionListenerSkippedWhenNotValidationException(): void
    {
        $this->serializer->expects($this->never())
            ->method('serialize');

        $event = $this->createExceptionEvent(new Exception());

        (new ValidationExceptionListener($this->serializer))($event);
    }

    public function testValidationExceptionListenerSuccess(): void
    {
        $serialized = json_encode([
            'message' => 'validation field',
            'details' => [
                'violations' => [
                    ['field' => 'some', 'message' => 'error'],
                ]
            ]
        ]);

        $violations = new ConstraintViolationList([
            new ConstraintViolation('error', null, [], null, 'some', null)
        ]);
        $event = $this->createExceptionEvent(new ValidationException(new ConstraintViolationList($violations)));
        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with(
                $this->callback(function (ErrorResponse $response) {
                    /** @var ErrorValidationsDetails|object $details */
                    $details = $response->getDetails();

                    if (!($details instanceof ErrorValidationsDetails)) {
                        return false;
                    }

                    $violations = $details->getViolations();
                    if (1 !== count($violations) || 'validation failed' !== $response->getMessage()) {
                        return false;
                    }

                    return 'some' === $violations[0]->getField()
                        && 'error' === $violations[0]->getMessage();
                }),
                JsonEncoder::FORMAT
            )
            ->willReturn($serialized);

        (new ValidationExceptionListener($this->serializer))($event);

        $this->assertResponse(Response::HTTP_BAD_REQUEST, $serialized, $event->getResponse());
    }
}
