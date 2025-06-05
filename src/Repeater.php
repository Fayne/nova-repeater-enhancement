<?php

namespace MobileNowGroup\Repeater;

use Laravel\Nova\Fields\Repeater as BaseRepeater;

class Repeater extends BaseRepeater
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'repeater';
}
