<?php

namespace DaiyanMozumder\LaravelFlexSearch;

use Illuminate\Database\Eloquent\Builder;

class FlexSearch
{
    /**
     * Apply dynamic filters, relationship filters, and keyword search to any model.
     *
     * @param Builder $query
     * @param array $filters           Key-value filters (with optional operators, supports dot notation)
     * @param string|null $searchTerm  Global keyword search term
     * @param array $searchableColumns Columns to be included in keyword search (supports dot notation)
     * @return Builder
     */
    public function apply(
        Builder $query,
        array $filters = [],
        ?string $searchTerm = null,
        array $searchableColumns = []
    ) {
        // Apply key-value filters (with support for comparison operators and relations using dot notation)
        foreach ($filters as $field => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            // Detect operator in key (e.g., "budget<=", "price>=", "created_at!=")
            if (preg_match('/^([a-zA-Z0-9_\.]+)([<>!=]+)$/', $field, $matches)) {
                [$full, $column, $operator] = $matches;
            } else {
                $column = $field;
                $operator = '=';
            }

            // Handle relations with dot notation (e.g., "company.name")
            if (str_contains($column, '.')) {
                [$relation, $relColumn] = explode('.', $column);
                $query->whereHas($relation, function ($q) use ($relColumn, $operator, $value) {
                    $q->where($relColumn, $operator, $value);
                });
            } else {
                $query->where($column, $operator, $value);
            }
        }

        // Apply global search term
        if (!empty($searchTerm) && !empty($searchableColumns)) {
            $terms = explode(' ', $searchTerm);

            $query->where(function ($q) use ($terms, $searchableColumns) {
                foreach ($terms as $term) {
                    $q->where(function ($inner) use ($term, $searchableColumns) {
                        foreach ($searchableColumns as $column) {
                            if (str_contains($column, '.')) {
                                [$relation, $relColumn] = explode('.', $column);
                                $inner->orWhereHas($relation, function ($q) use ($relColumn, $term) {
                                    $q->where($relColumn, 'like', "%{$term}%");
                                });
                            } else {
                                $inner->orWhere($column, 'like', "%{$term}%");
                            }
                        }
                    });
                }
            });
        }

        return $query;
    }
}
