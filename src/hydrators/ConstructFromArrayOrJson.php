<?php

namespace BaseHelpers\hydrators;

abstract class ConstructFromArrayOrJson
{
    /**
     * @param string(json)|array|null $params
     */
    public function __construct($params = null)
    {
        if (is_string($params)) {
            $params = json_decode($params, true);
        }
        if (!is_array($params)) {
            return;
        }
        foreach ($params as $property => $value) {
            $setter = 'set' . str_replace('_', '', $property);
            if (method_exists($this, $setter)) {
                $this->{$setter}($value);
            } elseif (property_exists($this, $property)) {
                $this->{$property} = $value;
            }
        }
    }

    /**
     * @param bool $withNull
     *
     * @return array
     */
    public function toArray($withNull = false)
    {
        $result = [];
        foreach (get_object_vars($this) as $property => $value) {
            $getter = 'get' . str_replace('_', '', $property);
            if (method_exists($this, $getter)) {
                $result[$property] = $this->{$getter}();
            } else {
                $result[$property] = $this->{$property};
            }
        }
        if ($withNull) {
            return $result;
        }

        return array_filter(
            $result, function ($value) {
            return isset($value);
        }
        );
    }

    /**
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }
}