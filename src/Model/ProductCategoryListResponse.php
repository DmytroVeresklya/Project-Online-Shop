<?php

namespace App\Model;

class ProductCategoryListResponse
{
    /**
     * @param ProductCategoryListItem[] $items
     */
    public function __construct(
        private readonly array $items,
    ) {
    }

    /**
     * @return ProductCategoryListItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
