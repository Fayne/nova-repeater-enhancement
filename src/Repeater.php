<?php

namespace MobileNowGroup\Repeater;

use Laravel\Nova\Fields\Repeater as BaseRepeater;
use Laravel\Nova\Fields\Searchable;
use Laravel\Nova\Http\Requests\NovaRequest;

class Repeater extends BaseRepeater
{
    use Searchable;

    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'repeater';

    /**
     * Create a new field.
     *
     * @param  \Stringable|string  $name
     * @param  (callable(mixed, mixed, ?string):mixed)|null  $resolveCallback
     */
    public function __construct($name, ?string $attribute = null, ?callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->onlyOnForms();
        $this->repeatables = RepeatableCollection::make();
    }

    /**
     * Use the JSON preset for the field.
     *
     * @return $this
     */
    public function asJson()
    {
        return $this->preset(new JSON)
            ->onlyOnForms()
            ->showOnDetail();
    }

    /**
     * Prepare the field for JSON serialization.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $searchable = $this->isSearchable(app(NovaRequest::class));

        return array_merge([
            'repeatables' => $this->repeatables,
            'sortable' => $this->sortable,
            'searchable' => $searchable,
        ], parent::jsonSerialize());
    }
}
