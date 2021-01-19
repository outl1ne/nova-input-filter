<?php declare(strict_types=1);

namespace OptimistDigital\NovaInputFilter;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class InputFilter extends Filter
{
    public $component = 'nova-input-filter';
    public $options = [];

    public function __construct($options = null, $name = null)
    {
        if (!empty($options)) $this->options = $options;
        if (!empty($name)) $this->name = $name;
    }

    public function apply(Request $request, $query, $search)
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

    public function options(Request $request)
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
}
