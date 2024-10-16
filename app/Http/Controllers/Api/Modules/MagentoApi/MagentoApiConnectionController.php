<?php

namespace App\Http\Controllers\Api\Modules\MagentoApi;

use App\Http\Controllers\Controller;
use App\Http\Requests\MagentoApiConnectionUpdateRequest;
use App\Http\Resources\MagentoConnectionResource;
use App\Models\Configuration;
use App\Modules\Magento2API\PriceSync\src\Http\Requests\MagentoApiConnectionDestroyRequest;
use App\Modules\Magento2API\PriceSync\src\Http\Requests\MagentoApiConnectionIndexRequest;
use App\Modules\Magento2API\PriceSync\src\Http\Requests\MagentoApiConnectionStoreRequest;
use App\Modules\Magento2API\PriceSync\src\Models\MagentoConnection;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\Tags\Tag;

class MagentoApiConnectionController extends Controller
{
    public function index(MagentoApiConnectionIndexRequest $request): AnonymousResourceCollection
    {
        $query = MagentoConnection::getSpatieQueryBuilder();

        return MagentoConnectionResource::collection($this->getPaginatedResult($query));
    }

    public function store(MagentoApiConnectionStoreRequest $request): MagentoConnectionResource
    {
        $connection = new MagentoConnection;
        $connection->fill($request->only($connection->getFillable()));

        if ($request->has('tag')) {
            $tag = Tag::findOrCreate($request->get('tag'));
            $connection->inventory_source_warehouse_tag_id = $tag->getKey();
        }

        $connection->save();

        Configuration::query()->update(['ecommerce_connected' => true]);

        return new MagentoConnectionResource($connection);
    }

    public function update(MagentoApiConnectionUpdateRequest $request, MagentoConnection $connection): MagentoConnectionResource
    {
        $connection->fill($request->validated());

        if ($request->tag) {
            $tag = Tag::findOrCreate($request->tag);
            $connection->inventory_source_warehouse_tag_id = $tag->id;
            $connection->tags()->sync([$tag->id]);
        }

        $connection->save();

        return new MagentoConnectionResource($connection);
    }

    public function destroy(MagentoApiConnectionDestroyRequest $request, MagentoConnection $connection): MagentoConnectionResource
    {
        $connection->delete();

        return MagentoConnectionResource::make($connection);
    }
}
