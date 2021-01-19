<?php declare(strict_types=1);

namespace OptimistDigital\NovaInputFilter;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class InputFilter extends Filter
{
    public $component = 'nova-input-filter';
    public array $options = [];

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
            $canSearchPrimaryKey = is_numeric($search) &&
                in_array($query->getModel()->getKeyType(), ['int', 'integer']) && ($connectionType != 'pgsql' || $search <= PHP_INT_MAX) &&
                in_array($query->getModel()->getKeyName(), static::$search);

            if ($canSearchPrimaryKey) {
                $query->orWhere($query->getModel()->getQualifiedKeyName(), $search);
            }

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
}
