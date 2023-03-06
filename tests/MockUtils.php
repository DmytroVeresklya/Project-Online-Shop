<?php

namespace App\Tests;

use App\Entity\Product;
use App\Entity\ProductCategory;
use App\Entity\User;

class MockUtils
{
    public static function createUser(): User
    {
        return (new User())
            ->setEmail('testEmail@test.com')
            ->setFirstName('testFirstName')
            ->setLastName('testLastName')
            ->setPhoneNumber('1234567890')
            ->setRoles(['ROLE_EDITOR']);
    }

    public static function createProductCategory(): ProductCategory
    {
        return (new ProductCategory())->setTitle('testTitle')->setSlug('testslug')->setImage('testImage');
    }

    public static function createProduct(ProductCategory $productCategory = null): Product
    {
        return (new Product())
            ->setTitle('test Title')
            ->setDescription('testDescription')
            ->setAmount(1)
            ->setPrice(300)
            ->setSlug('test-title')
            ->setProductCategory($productCategory)
            ->setMadeIn('Ukraine');
    }
}
