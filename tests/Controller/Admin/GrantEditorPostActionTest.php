<?php

namespace App\Tests\Controller\Admin;

use App\Tests\AbstractControllerTest;

class GrantEditorPostActionTest extends AbstractControllerTest
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testGrandEditor(): void
    {
        $user = $this->getUser('user@test.com', 'password');

        $email    = 'admin@test.com';
        $password = 'password';
        $this->getAdmin($email, $password);

        $this->auth($email, $password);

        $this->client->request('POST', '/api/admin/grantEditor/' . $user->getId());

        $this->assertResponseIsSuccessful();
    }
}
