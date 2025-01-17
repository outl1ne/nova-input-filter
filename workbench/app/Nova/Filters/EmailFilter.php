<?php

use Illuminate\Contracts\Database\Eloquent\Builder;
use Laravel\Nova\Http\Requests\NovaRequest;
use Outl1ne\NovaInputFilter\InputFilter;

class EmailFilter extends InputFilter
{
    public function apply(NovaRequest $request, Builder $query, mixed $value)
    {
        return $query->where('email', 'like', "%$value%");
    }
}
