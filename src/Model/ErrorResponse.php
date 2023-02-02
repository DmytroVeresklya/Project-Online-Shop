<?php

namespace App\Model;

use App\Model\ErrorDebugDetails;
use App\Model\ErrorValidationsDetails;
use OpenApi\Attributes as OA;

class ErrorResponse
{
    public function __construct(
        private string $message,
        private mixed  $details = null
    ) {
    }

    #[OA\Property(type: 'object', oneOf: [
        new OA\Schema(type: ErrorDebugDetails::class),
        new OA\Schema(type: ErrorValidationsDetails::class)
    ])]
    public function getDetails(): mixed
    {
        return $this->details;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
