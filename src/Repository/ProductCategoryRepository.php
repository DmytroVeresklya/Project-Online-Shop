<?php

namespace App\Repository;

use App\Entity\ProductCategory;
use App\Exception\ProductCategoryNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProductCategory>
 *
 * @method null|ProductCategory find($id, $lockMode = null, $lockVersion = null)
 * @method null|ProductCategory findOneBy(array $criteria, array $orderBy = null)
 * @method ProductCategory[]    findAll()
 * @method ProductCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductCategory::class);
    }

    public function save(ProductCategory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ProductCategory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return ProductCategory[]
     */
    public function findAllSortByTitle(): array
    {
        return $this->findBy([], ['title' => Criteria::ASC]);
    }

    public function existById(int $id): bool
    {
        return null !== $this->find($id);
    }

    public function getProductCategoryByTitle(?string $productCategory): ?ProductCategory
    {
        return $productCategory ? $this->findOneBy(['title' => $productCategory]) : null;
    }

    public function getProductCategoryById(int $id): ProductCategory
    {
        $productCategory = $this->find($id);
        if (!$productCategory) {
            throw new ProductCategoryNotFoundException();
        }

        return $productCategory;
    }

    public function countProductsInCategory(int $id): int
    {
        $productCategory = $this->getProductCategoryById($id);

        return count($productCategory->getProducts());
    }

    public function existBySlug(?string $slug): bool
    {
        return null !== $this->findOneBy(['slug' => $slug]);
    }
}
