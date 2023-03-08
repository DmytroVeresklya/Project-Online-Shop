<?php

namespace App\Tests\Controller;

use App\Controller\AuthController;
use App\Tests\AbstractControllerTest;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthControllerTest extends AbstractControllerTest
{
    public function testSignUp(): void
    {
        $data = json_encode([
            'firstName'       => 'Test_First_Name',
            'lastName'        => 'Test_last_Name',
            'email'           => 'Test_Email@test.com',
            'phoneNumber'     => '380912345678',
            'password'        => 'Test_Password',
            'confirmPassword' => 'Test_Password'
        ]);

        $this->client->request(Request::METHOD_POST, '/api/signUp', [], [], [], $data);
        $responseContent = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseIsSuccessful();
        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => ['token', 'refresh_token'],
            'properties' => [
                'token' => ['type' => 'string'],
                'refresh_token' => ['type' => 'string'],
            ],
        ]);
    }
}
