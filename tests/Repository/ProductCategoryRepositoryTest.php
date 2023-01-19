<?php

namespace App\Tests\Repository;

use App\Entity\ProductCategory;
use App\Repository\ProductCategoryRepository;
use App\Tests\AbstractRepositoryTest;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

/**
 * @internal
 *
 * @coversNothing
 */
class ProductCategoryRepositoryTest extends AbstractRepositoryTest
{
    private ProductCategoryRepository $productCategoryRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productCategoryRepository = $this->em->getRepository(ProductCategory::class);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function testFindAllSortByTitle()
    {
        $productB = (new ProductCategory())->setTitle('B')->setSlug('B');
        $productD = (new ProductCategory())->setTitle('D')->setSlug('D');
        $productA = (new ProductCategory())->setTitle('A')->setSlug('A');
        $productC = (new ProductCategory())->setTitle('C')->setSlug('C');

        foreach ([$productB, $productD, $productA, $productC] as $productCategory) {
            $this->em->persist($productCategory);
        }

        $this->em->flush();

        $titles = array_map(
            fn (ProductCategory $productCategory) => $productCategory->getTitle(),
            $this->productCategoryRepository->findAllSortByTitle()
        );

        $this->assertEquals(['A', 'B', 'C', 'D'], $titles);
    }
}
