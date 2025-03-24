<?php


namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{
    public function storeProducts(array $products)
    {
        $products['created_date'] = now();
        $products['updated_date'] = now();

        Product::create($products);
    }
}
