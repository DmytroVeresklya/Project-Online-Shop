<?php

namespace App\Tests\Controller\View;

use App\Tests\AbstractControllerTest;
use App\Tests\MockUtils;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

class MainPageGetActionTest extends AbstractControllerTest
{
    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function testProductCategoryGetProductsGetActionSuccess(): void
    {
        $this->em->persist(
            MockUtils::createProductCategory()
        );
        $this->em->flush();

        $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains(selector: 'title', text: 'LuckyDog');
        $this->assertSelectorTextContains(selector: 'span', text: 'LuckyDog');
        $this->assertSelectorTextContains(selector: 'button', text:  'Ввійти');
        $this->assertSelectorTextContains(
            selector: 'ul',
            text:     'Головна Товари та послуги Доставка і оплата Умови обміну і повернення Про нас'
        );
        $this->assertSelectorExists(selector: 'img');
    }
}
