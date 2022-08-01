<?php

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class LocationFilter extends ModelFilter
{
    /**
     * Related Models that have ModelFilters as well as the method on the ModelFilter
     * As [relationMethod => [input_key1, input_key2]].
     *
     * @var array
     */
    public $relations = [];

    public static function splitStringByCommaAndSpace(string $s)
    {
        $pattern = '/[\s+,]/';
        return preg_split($pattern, $s, -1, PREG_SPLIT_NO_EMPTY);
    }


    public function user($id)
    {
        return $this->where('user_id', $id);
    }
    public function activity($activity)
    {
        return $this->where('allowed_activities', 'LIKE', "%$activity%");
    }

    public function address($addresses)
    {
        $addresses = static::splitStringByCommaAndSpace($addresses);
        $filter = $this;
        for ($i = 0; $i < count($addresses); $i++) {
            $address = $addresses[$i];
            if ($i == 0) {
                $filter = $filter->where('country', 'LIKE', "%$address%")
                    ->orWhere('state', 'LIKE', "%$address%")
                    ->orWhere('city', 'LIKE', "%$address%")
                    ->orWhere('street', 'LIKE', "%$address%")
                    ->orWhere('zip_code', 'LIKE', "%$address%");
                continue;
            }
            $filter = $filter->orWhere('country', 'LIKE', "%$address%")
                ->orWhere('state', 'LIKE', "%$address%")
                ->orWhere('city', 'LIKE', "%$address%")
                ->orWhere('street', 'LIKE', "%$address%")
                ->orWhere('zip_code', 'LIKE', "%$address%");
        }

        return $filter;
    }

    public function crew($range)
    {
        $min = intval(explode('-', $range)[0]);
        return $this->where('maximum_attendees_number', '>=', $min);
    }

    public function types($types)
    {
        $types = static::splitStringByCommaAndSpace($types);
        $filter = $this;
        for ($i = 0; $i < count($types); $i++) {
            $type = $types[$i];
            if ($i == 0) {
                $filter = $filter->where('types', 'LIKE', "%$type%");
                continue;
            }
            $filter = $filter->orWhere('types', 'LIKE', "%$type%");
        }
        return $filter;
    }

    public function styles($styles)
    {
        $styles = static::splitStringByCommaAndSpace($styles);
        $filter = $this;
        for ($i = 0; $i < count($styles); $i++) {
            $style = $styles[$i];
            if ($i == 0) {
                $filter = $filter->where('styles', 'LIKE', "%$style%");
                continue;
            }
            $filter = $filter->orWhere('styles', 'LIKE', "%$style%");
        }
        return $filter;
    }

    public function features($features)
    {
        $features = static::splitStringByCommaAndSpace($features);
        $filter = $this;
        for ($i = 0; $i < count($features); $i++) {
            $feature = $features[$i];
            if ($i == 0) {
                $filter = $filter->where('features', 'LIKE', "%$feature%");
                continue;
            }
            $filter = $filter->orWhere('features', 'LIKE', "%$feature%");
        }
        return $filter;
    }
}
