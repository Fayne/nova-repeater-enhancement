<?php

namespace MobileNowGroup\Repeater;

use Laravel\Nova\Fields\Repeater\RepeatableCollection as BaseRepeatableCollection;

class RepeatableCollection extends BaseRepeatableCollection
{
    /**
     * Find a Repeatable class by its key.
     *
     * @param  string  $key
     * @return \Laravel\Nova\Fields\Repeater\Repeatable
     */
    public function findByKey($key)
    {
        return $this->first(function ($item) use ($key) {
            if (method_exists($item, 'getKey')) {
                return $item->getKey() === $key;
            } else {
                return $item->key() === $key;
            }
        });
    }

    /**
     * Return a new instance of a Repeatable by its key.
     *
     * @param  string  $key
     * @param  \Illuminate\Database\Eloquent\Model|array  $data
     * @return \Laravel\Nova\Fields\Repeater\Repeatable
     */
    public function newRepeatableByKey($key, $data = [])
    {
        $block = $this->findByKey($key);

        if (! $block) {
            $block = config('nova.repeater.fallback_class');
        }

        if (is_a($block, config('nova.repeater.fallback_class'))) {
            return new $block($key, $data);
        }

        return new $block($data);
    }
}
