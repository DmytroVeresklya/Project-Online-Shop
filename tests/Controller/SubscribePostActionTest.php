<?php

namespace App\Tests\Controller;

use App\Tests\AbstractControllerTest;
use Symfony\Component\HttpFoundation\Response;

class SubscribePostActionTest extends AbstractControllerTest
{
    public function testSubscribeControllerSuccess(): void
    {
        $content = json_encode(['email' => 'test@test.com', 'agreed' => true]);
        $this->client->request('POST', '/api/subscribe', [], [], [], $content);

        $this->assertResponseIsSuccessful();
    }

    public function testSubscribeControllerValidationErrorNotAgreed(): void
    {
        $content = json_encode(['email' => 'test@test.com']);
        $this->client->request('POST', '/api/subscribe', [], [], [], $content);
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertJsonDocumentMatches($responseContent, [
            '$.message' => 'validation failed',
            '$.details.violations' => self::countOf(1),
            '$.details.violations[0].field' => 'agreed',
        ]);
    }
}
