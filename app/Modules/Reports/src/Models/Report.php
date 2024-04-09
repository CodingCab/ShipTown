<?php

namespace App\Modules\Reports\src\Models;

use App\Helpers\CsvBuilder;
use App\Modules\Reports\src\Http\Resources\ReportResource;
use File;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use League\Csv\Exception;
use Spatie\QueryBuilder\Exceptions\InvalidFilterQuery;

class Report extends ReportBase
{
    public function response($request = null): mixed
    {
        switch (File::extension(request('filename'))) {
            case 'csv':
                return $this->toCsvFileDownload();
            case 'json':
                return $this->toJsonResource();
            default:
                return $this->view();
        }
    }

    protected function view(): mixed
    {
        try {
            $this->perPage = request('per_page', 50);

            $records = $this->getFinalQuery()->get();

            if (empty($this->fields) && $records->isNotEmpty()) {
                $this->fields = $records->first()->toArray();
            }

            $data = [
                'data' => ReportResource::collection($records),
                'meta' => [
                    'report_name' => $this->report_name ?? $this->table,
                    'fields' =>  array_keys($this->fields),
                    'pagination' => [
                        'per_page' => $this->perPage,
                        'page' => request('page', 1),
                    ],
                    'field_links' => $this->getFieldLinks(array_keys($this->fields))
                ],
            ];

            $view = request('view', $this->view);

            return view($view, $data);
        } catch (InvalidFilterQuery $ex) {
            return response($ex->getMessage(), $ex->getStatusCode());
        }
    }

    public function toJsonResource(): JsonResource
    {
        return JsonResource::make($this->getFinalQuery()->get());
    }

    public static function json(): JsonResource
    {
        $report = new static();

        return $report->toJsonResource();
    }
    /**
     * @throws Exception
     */
    public function toCsvFileDownload(): Response|Application|ResponseFactory
    {
        $csv = CsvBuilder::fromQueryBuilder($this->queryBuilder());

        return response((string)$csv, 200, [
            'Content-Type' => 'text/csv',
            'Cache-Control' => 'no-store, no-cache',
            'Content-Transfer-Encoding' => 'binary',
            'Content-Disposition' => 'attachment; filename="' . request('filename', 'report.csv') . '"',
        ]);
    }

    /**
     * @param $fields
     * @return Collection
     */
    public function getFieldLinks($fields): Collection
    {
        return collect($fields)->map(function ($field) {
            $sortIsDesc = request()->has('sort') && str_starts_with(request()->sort, '-');
            $currentSortName = str_replace('-', '', request()->sort);
            $isCurrent = $currentSortName === $field;
            $url = request()->fullUrlWithQuery(['sort' => $isCurrent && !$sortIsDesc ? "-" . $field : $field]);

            return [
                'name' => $field,
                'url' => $url,
                'is_current' => $isCurrent,
                'is_desc' => $sortIsDesc,
                'display_name' => str_replace('_', ' ', ucwords($field, '_')),
                'type' => $this->getFieldType($field),
                'operators' => $this->getFieldTypeOperators($field),
            ];
        });
    }
}
