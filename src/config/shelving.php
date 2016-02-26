<?php

return [
    /**
     * Classmap to direct query results
     * to the correct property
     *
     * Defaults to Laravel classes, but can be overridden
     * by adding a base shelf class with a classmap property
     */
    'classmap' => [
        'Illuminate\Database\Eloquent\Builder' => 'query',
        'Illuminate\Database\Eloquent\Collection' => 'collection',
        'Illuminate\Support\Collection' => 'collection',
        'Illuminate\Database\Eloquent\Model' => 'instance'
    ]
];
