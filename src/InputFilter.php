<?php

namespace Outl1ne\NovaInputFilter;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

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
        if (! empty($options)) {
            $this->forColumns($options);
        }

        if (! empty($name)) {
            $this->withName($name);
        }
    }

    public function apply(NovaRequest $request, Builder $query, mixed $value)
    {
        return $query->where(function ($query) use ($value) {
            $model = $query->getModel();
            $connectionType = $query->getModel()->getConnection()->getDriverName();

            $likeOperator = $connectionType == 'pgsql' ? 'ilike' : 'like';

            $query->orWhere(function ($query) use ($value, $model, $likeOperator) {
                foreach ($this->options as $column) {
                    $qColumn = $model->qualifyColumn($column);
                    $qValue = mb_strtoupper($value);
                    $query->orWhereRaw("UPPER($qColumn) $likeOperator ?", ["%$qValue%"]);
                }
            });
        });
    }

    public function forColumns($columns)
    {
        if (! is_array($columns)) {
            $columns = [$columns];
        }
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

        if (! empty($this->name) && ! $isUniqueClass) {
            return get_class($this).str_replace(' ', '', $this->name);
        }

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
