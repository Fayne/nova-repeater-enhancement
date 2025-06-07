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
