<?php

namespace App\Modules\Reports\src\Models;

use Spatie\Activitylog\Models\Activity;
use Spatie\QueryBuilder\AllowedInclude;

class ActivityReport extends Report
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->report_name = 'Activity Log';
        $this->defaultSort = '-id';

        $this->baseQuery = Activity::query()
            ->leftJoin('users', function ($join) {
                $join->on('activity_log.causer_id', '=', 'users.id')
                    ->where('activity_log.causer_type', 'App\User');
            });

        $this->fields = [
            'id' => 'activity_log.id',
            'created_at' => 'activity_log.created_at',
            'description' => 'activity_log.description',
            'subject_type' => 'activity_log.subject_type',
            'subject_id' => 'activity_log.subject_id',
            'causer_id' => 'activity_log.causer_id',
            'causer_type' => 'activity_log.causer_type',
            'user_name' => 'users.name',
            'properties' => 'activity_log.properties',
        ];

        $this->casts = [
            'created_at' => 'datetime',
        ];

        $this->addAllowedInclude(AllowedInclude::relationship('causer'));
    }
}
