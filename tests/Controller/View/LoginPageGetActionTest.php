<?php

namespace App\Tests\Controller\View;

use App\Tests\AbstractControllerTest;
use App\Tests\MockUtils;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

class LoginPageGetActionTest extends AbstractControllerTest
{
    public function testProductCategoryGetProductsGetActionSuccess(): void
    {
        $this->client->request('GET', '/api/login');

        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains(selector: 'title', text: 'Log in');
        $this->assertSelectorTextContains(selector: 'span', text: 'LuckyDog');
        $this->assertSelectorTextContains(selector: 'button', text:  'Ввійти');
        $this->assertSelectorTextContains(
            selector: 'ul',
            text:     'Головна Товари та послуги Доставка і оплата Умови обміну і повернення Про нас'
        );
        $this->assertSelectorTextContains(selector: 'label', text:  'Log In');
        $this->assertSelectorExists(selector: 'input');
        $this->assertSelectorExists(selector: 'form');

    }
}
