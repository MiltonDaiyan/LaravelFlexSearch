<?php

namespace DaiyanMozumder\LaravelFlexSearch;

use Illuminate\Database\Eloquent\Builder;

class FlexSearch
{
    /**
     * Apply dynamic filters and keyword search to any model.
     *
     * @param Builder $query
     * @param array $filters
     * @param string|null $searchTerm
     * @param array $searchableColumns
     * @return Builder
     */
    public function apply(Builder $query, array $filters = [], ?string $searchTerm = null, array $searchableColumns = [])
    {
        // Apply key-value filters
        foreach ($filters as $field => $value) {
            if (!empty($value)) {
                $query->where($field, $value);
            }
        }

        // Apply global search term
        if (!empty($searchTerm) && !empty($searchableColumns)) {
            $terms = explode(' ', $searchTerm);

            $query->where(function ($q) use ($terms, $searchableColumns) {
                foreach ($terms as $term) {
                    $q->where(function ($inner) use ($term, $searchableColumns) {
                        foreach ($searchableColumns as $column) {
                            $inner->orWhere($column, 'like', "%{$term}%");
                        }
                    });
                }
            });
        }

        return $query;
    }
}
