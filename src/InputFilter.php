<?php

declare(strict_types=1);

namespace OptimistDigital\NovaInputFilter;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Filters\Filter;

class InputFilter extends Filter
{
    public $component = 'nova-input-filter';
    public $options = [];
    public $inputType = 'text';
    public $inputIntegersOnly = false;
    public $inputMin = false;
    public $inputMax = false;

    public function __construct($options = null, $name = null)
    {
        if (!empty($options)) $this->forColumns($options);
        if (!empty($name)) $this->withName($name);
    }

    public function apply(NovaRequest $request, $query, $search)
    {
        return $query->where(function ($query) use ($search) {
            $model = $query->getModel();
            $connectionType = $query->getModel()->getConnection()->getDriverName();

            $likeOperator = $connectionType == 'pgsql' ? 'ilike' : 'like';

            $query->orWhere(function ($query) use ($search, $model, $likeOperator) {
                foreach ($this->options as $column) {
                    $qColumn = $model->qualifyColumn($column);
                    $qSearch = mb_strtoupper($search);
                    $query->orWhereRaw("UPPER($qColumn) $likeOperator ?", ["%$qSearch%"]);
                }
            });
        });
    }

    public function forColumns($columns)
    {
        if (!is_array($columns)) $columns = [$columns];
        $this->options = $columns;
        return $this;
    }

    public function withName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function asNumber($number = true, $integersOnly = false)
    {
        $this->inputType = $number ? 'number' : 'text';
        $this->inputIntegersOnly = $integersOnly;

        return $this;
    }

    public function options(NovaRequest $request)
    {
        return $this->options;
    }

    public function key()
    {
        $isUniqueClass = get_class($this) !== InputFilter::class;

        if (!empty($this->name) && !$isUniqueClass)
            return get_class($this) . str_replace(' ', '', $this->name);

        return parent::key();
    }

    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'input_type' => $this->inputType,
            'input_integers' => $this->inputIntegersOnly,
            'input_min' => $this->inputMin,
            'input_max' => $this->inputMax,
        ]);
    }
}
