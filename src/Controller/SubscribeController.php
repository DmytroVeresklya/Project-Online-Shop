<?php

namespace App\Controller;

use App\Attribute\RequestBody;
use App\Model\ErrorResponse;
use App\ModelItem\SubscribeRequest;
use App\Service\SubscribeService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/subscribe', methods: ['POST'])]
#[
    OA\Response(
        response: 200,
        description: 'Subscribe email to newsletter mailing list',
    ),
    OA\Response(
        response: 409,
        description: 'Subscriber already exists',
        content: new Model(type: ErrorResponse::class)
    ),
    OA\Response(
        response: 400,
        description: 'Validation failed',
        content: new Model(type: ErrorResponse::class)
    ),
    OA\RequestBody(content: new Model(type: SubscribeRequest::class))
]
class SubscribeController extends AbstractController
{
    public function __construct(private readonly SubscribeService $subscriberService)
    {
    }

    public function __invoke(#[RequestBody] SubscribeRequest $subscriberRequest): Response
    {
        $this->subscriberService->subscribe($subscriberRequest);

        return $this->json(null);
    }
}
