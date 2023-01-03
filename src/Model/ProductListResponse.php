<?php

namespace App\Model;

use App\ModelItem\ProductListItem;

class ProductListResponse
{
    /**
     * @param ProductListItem[] $items
     */
    public function __construct(
        private readonly array $items,
    ) {}

    /**
     * @return ProductListItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}