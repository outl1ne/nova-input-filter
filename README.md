# Nova Input Filter

[![Latest Version on Packagist](https://img.shields.io/packagist/v/outl1ne/nova-input-filter.svg?style=flat-square)](https://packagist.org/packages/outl1ne/nova-input-filter)
[![Total Downloads](https://img.shields.io/packagist/dt/outl1ne/nova-input-filter.svg?style=flat-square)](https://packagist.org/packages/outl1ne/nova-input-filter)

This [Laravel Nova](https://nova.laravel.com/) package allows you to create simple input filters.

## Requirements

- `php: >=8.1`
- `laravel/nova: ^5.0`

## Features

- Out of the box, works like an additional search field.
- Inline usage for simple use-cases.

## Screenshots

![Input filter](./docs/input-filter.gif)

## Installation

Install the package in to a Laravel app that uses [Nova](https://nova.laravel.com) via composer:

```bash
composer require outl1ne/nova-input-filter
```

## Usage

Accepts an array of columns as first parameter and filter name as second parameter. Can optionally pass in multiple
columns: `['email', 'id']`, similarly to nova's search.

```php
use Outl1ne\NovaInputFilter\InputFilter;
use Laravel\Nova\Http\Requests\NovaRequest;

// ..

public function filters(NovaRequest $request): array
{
    return [
        InputFilter::make()->forColumns(['email'])->withName('Email'),

        // Or

        InputFilter::make(['email'], 'email'),
    ];
}
```

## Customizing

Out of the box, `InputFilter` works exactly like Nova's search field. If you wish to change it, you can extend
the `InputFilter` class and override `apply()` function.

```php

use Outl1ne\NovaInputFilter\InputFilter;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Laravel\Nova\Http\Requests\NovaRequest;

class ExtendedInputFilter extends InputFilter
{
    public function apply(NovaRequest $request, Builder $query, mixed $value)
    {
        return $query->where('email', 'like', "%$value%");
    }
}
```

## Credits

- [Kaspar Rosin](https://github.com/kasparrosin)

## License

Nova Input Filter is open-sourced software licensed under the [MIT license](LICENSE.md).
