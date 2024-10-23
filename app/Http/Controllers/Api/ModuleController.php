<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ModuleUpdateRequest;
use App\Http\Resources\ModuleResource;
use App\Models\Module;
use App\Modules\Reports\src\Models\ModuleReport;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ModuleController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $modules = new ModuleReport;

        return ModuleResource::collection($modules->queryBuilder()->get());
    }

    public function update(ModuleUpdateRequest $request, int $module_id): ModuleResource
    {
        $module = Module::query()->findOrFail($module_id);

        $module->update($request->validated());

        return ModuleResource::make($module->refresh());
    }
}
