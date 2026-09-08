<?php

namespace Sereny\NovaPermissions\Fields;

use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

class Checkboxes extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'permission-checkboxes';


    /**
     * Create a new field.
     *
     * @param  string  $name
     * @param  string|\Closure|callable|object|null  $attribute
     * @param  (callable(mixed, mixed, ?string):(mixed))|null  $resolveCallback
     * @return void
     */
    public function __construct($name, $attribute = null, callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->resolveUsing(function ($value) {
            return $value?->pluck('name')->values()->all() ?? [];
        });
    }

    /**
     * Specify the available options
     *
     * @param  array  $options
     * @return self
     */
    public function options(array $options)
    {
        return $this->withMeta(['options' => $options]);
    }

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest $request
     * @param  string                                  $requestAttribute
     * @param  object                                  $model
     * @param  string                                  $attribute
     * @return void
     */
    protected function fillAttributeFromRequest(NovaRequest $request, string $requestAttribute, object $model, string $attribute): void
    {
        if (! $request->exists($requestAttribute)) {
            return;
        }

        $value = $request->input($requestAttribute, []);

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $value = is_array($decoded) ? $decoded : explode(',', $value);
        }

        $permissions = collect($value)
            ->filter(fn ($name) => is_string($name) && $name !== '')
            ->unique()
            ->values()
            ->all();

        $model->syncPermissions($permissions);
    }
}
