<?php

declare(strict_types=1);

namespace Infrastructure\Product\Repositories;

use App\Core\Repositories\BaseRepository;
use Domain\Business\Models\Business;
use Domain\Product\Data\IndexProductData;
use Domain\Product\Data\StoreProductData;
use Domain\Product\Data\UpdateProductData;
use Domain\Product\Interfaces\ProductRepositoryInterface;
use Domain\Product\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\LaravelData\Optional;

final readonly class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(private Product $model) {}

    public function getFilteredPaginated(IndexProductData $indexProductData, Business $business, ?array $relations = []): LengthAwarePaginator
    {
        return $this->model::query()
            ->with($relations)
            ->where('business_id', $business->id)
            ->filtered($indexProductData->filterQuery, ['name'])
            ->order($indexProductData->sort, $indexProductData->order)
            ->paginate(
                perPage: $indexProductData->perPage,
                page: $indexProductData->page,
            );
    }

    public function findById(int|string $id, ?array $relations = []): ?Product
    {
        return $this->model::query()
            ->with($relations)
            ->find($id);
    }

    public function store(StoreProductData $storeProductData, Business $business): Product
    {
        $product = $this->model->newInstance();
        $product->business_id = $business->id;
        $product->name = $storeProductData->name;
        $product->save();

        return $product;
    }

    public function update(UpdateProductData $updateProductData, Product $product): Product
    {
        if (! ($updateProductData->name instanceof Optional)) {
            $product->name = $updateProductData->name;
        }

        $product->save();

        return $product;
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }

    /**
     * @return Collection<int, Product>
     */
    public function findByIdsForBusiness(array $ids, Business $business): Collection
    {
        return $business->products()->whereIn('id', $ids)->get();
    }

    public function deleteMany(Collection $products): void
    {
        $this->model::whereIn('id', $products->pluck('id'))->delete();
    }
}
