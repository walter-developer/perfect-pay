<?php

namespace App\Support\Collection;

use Closure;
use App\Support\Collection\CollectionFormatter;
use Illuminate\Support\Collection as LaravelCollection;

class Collection extends LaravelCollection
{
    public function __construct(object|array $items = [])
    {
        $data = parent::getArrayableItems($items);
        $data = $this->getCollectionRecurssive($data);
        $this->items = $data;
    }

    public function collection()
    {
        return new LaravelCollection($this->all());
    }

    public function laravel()
    {
        return new LaravelCollection($this->all());
    }

    public function copy(string|int $search, string|int $attribute, mixed $default = null): static
    {
        return $this->set($attribute, $this->get($search, $default));
    }

    public function move(string|int $search, string|int $attribute, mixed $default = null): static
    {
        return $this->set($attribute, $this->get($search, $default))->delete($search);
    }

    public function forIn(Closure $callback, bool $reverseKey = true): static
    {
        return $this->foreach($callback, $reverseKey);
    }

    public function forInRecurssive(Closure $callback, bool $reverseKey = true): static
    {
        return $this->foreachRecurssive($callback, $reverseKey);
    }

    public function forInRecurssiveAll(Closure $callback, bool $reverseKey = true): static
    {
        return $this->foreachRecurssiveAll($callback, $reverseKey);
    }

    public function forInCollections(Closure $callback, bool $reverseKey = true): static
    {
        return $this->foreachInCollections($callback, $reverseKey);
    }

    public function forInCollectionsRecurssive(Closure $callback, bool $reverseKey = true): static
    {
        return $this->foreachInCollectionsRecurssive($callback, $reverseKey);
    }

    public function setItem(string|int $attribute, mixed $value = null): static
    {
        $this->items[$attribute] = $value;
        return $this;
    }

    public function mergeItem(mixed $value = null): static
    {
        $this->items[] = $value;
        return $this;
    }

    public function mergeItems(object|array $value = []): static
    {
        $this->__construct(array_merge($this->items, $value));
        return $this;
    }

    public function setItems(object|array $value = []): static
    {
        $this->__construct($value);
        return $this;
    }

    public function inArray(mixed $attribute = null, mixed $in = null): bool
    {
        $values = $attribute ? $this->get($attribute) : [];
        if (is_array($values) || is_object($values)) {
            $values = (array)$values;
            return in_array($in, $values);
        }
        return false;
    }

    public function compare(mixed $attribute = null, mixed $value = null, bool $identical = false): bool
    {
        if ($attribute && $this->exists($attribute)) {
            $attributeValue = $this->get($attribute);
            return ($identical ? ($attributeValue === $value) : ($attributeValue == $value));
        }
        return false;
    }

    public function equals(mixed $attributeA = null, mixed $attributeB = null, bool $identical = false): bool
    {
        if ($attributeA && $attributeB && $this->exists($attributeA) && $this->exists($attributeB)) {
            $valueA = $this->get($attributeA);
            $valueB = $this->get($attributeB);
            return ($identical ? ($valueA === $valueB) : ($valueA == $valueB));
        }
        return false;
    }

    public function if(mixed $attribute = null, mixed $value = null, mixed $if = true, mixed $else = false): mixed
    {
        if ($attribute && $this->exists($attribute)) {
            $attributeValue = $this->get($attribute);
            if ($attributeValue == $value) {
                return (($if && ($if instanceof Closure))
                    ? $if($attributeValue, $value, $this) : $if);
            }
        }
        return (($else && ($else instanceof Closure))
            ? $else($attributeValue, $value, $this) : $else);
    }

    public function getItem(mixed $attribute = null, mixed $default = null): mixed
    {
        return array_key_exists($attribute, $this->items) ?
            $this->items[$attribute] : $this->call($default);
    }

    public function getItems(): array
    {
        return  (array)$this->items;
    }

    public function getConcat(string|int $attribute, string|int|null $prefix = null, string|int|null $sufix = null): string
    {
        $value = $this->get($attribute);
        if ($value && (is_array($value) || is_object($value))) {
            return strval($prefix) . strval($sufix);
        }
        if (is_numeric($value) || is_bool($value)) {
            return strval($prefix) . $value . strval($sufix);
        }
        if (is_string(strval($value))) {
            return strval($prefix) . strval($value) . strval($sufix);
        }
        return strval($prefix) . strval($sufix);
    }

    public function getCollect(string|int $attribute, object|array $default = []): static
    {
        $value = $this->get($attribute, $default);
        if ($value && is_object($value) || is_array($value)) {
            if ($value instanceof static) {
                return $value;
            }
            return new static($value);
        }
        if ($default && is_object($default) || is_array($default)) {
            if ($default instanceof static) {
                return $default;
            }
            return new static($default);
        }
        return new static;
    }

    public function getArray(string|int $attribute, array $default = []): array
    {
        $value = $this->get($attribute, $default);
        if ($value && is_object($value) || is_array($value)) {
            return json_decode(json_encode($value), true) ?: $default;
        }
        return $default;
    }

    public function getObject(string|int $attribute, object $default = new stdClass): object
    {
        $value = $this->get($attribute, $default);
        if ($value && is_object($value) || is_array($value)) {
            return json_decode(json_encode($value), false) ?: $default;
        }
        return $default;
    }

    public function getLength(string|int $attribute): int
    {
        $value = $this->get($attribute);
        if ($value && is_object($value) || is_array($value)) {
            return count(json_decode(json_encode($value), true) ?: []);
        }
        if ($value && is_string(strval($value))) {
            return strlen(strval($value));
        }
        return 0;
    }

    public function getInteger(string|int $attribute, int|null $default = null): int|null
    {
        $value = $this->get($attribute);
        if (is_integer($value)) {
            return intval($value);
        }
        return $default;
    }

    public function getFloat(string|int $attribute, int|null $default = null): int|float|null
    {
        $value = $this->get($attribute);
        if (empty(is_array($value)) && empty(is_object($value)) && strlen($value)) {
            return floatval($value);
        }
        return $default;
    }

    public function getString(string|int $attribute, string|null $default = null): string|null
    {
        $value = $this->get($attribute);
        if (is_string(strval($value))) {
            return strval($value);
        }
        return $default;
    }

    public function getNumeric(string|int $attribute, int|float|null $default = null): int|float|null
    {
        $value = $this->get($attribute);
        if (is_numeric($value)) {
            return is_integer($value)
                ? (int)$value : (float)$value;
        }
        return $default;
    }

    public function isArray(mixed $attribute = null): bool
    {
        $values = ($attribute ? $this->get($attribute) : $this->items);
        $values = ($values && ($values instanceof static) ? $values?->all() : $values);
        $isArray = is_array($values);
        $isAssoc = $this->isAssoc($values);
        return $isArray && empty($isAssoc);
    }

    public function isCollection(mixed $attribute = null): bool
    {
        $values = ($attribute ? $this->get($attribute) : $this->items);
        return ($values instanceof static);
    }

    public function isObject(mixed $attribute = null): bool
    {
        $values = ($attribute ? $this->get($attribute) : $this->items);
        $values = ($values && ($values instanceof static) ? $values?->all() : $values);
        $isAssoc = $this->isAssoc($values);
        return $isAssoc;
    }

    public function isEmpty(mixed $attribute = null): bool
    {
        $values = ($attribute ? $this->get($attribute) : $this->items);
        $values = ($values && ($values instanceof static) ? $values?->all() : $values);
        return $attribute ? empty($values) : empty($this->items);
    }

    public function isNotEmpty(mixed $attribute = null): bool
    {
        $values = ($attribute ? $this->get($attribute) : $this->items);
        $values = ($values && ($values instanceof static) ? $values?->all() : $values);
        return $attribute ? !empty($values) : !empty($this->items);
    }

    public function length(string|int $attribute): int
    {
        $values = ($attribute ? $this->get($attribute) : $this->items);
        if (is_array($values) || is_object($values)) {
            return count((array)$values);
        }
        if (empty(is_null($values))) {
            return strlen($values);
        }
        return 0;
    }

    public function all(mixed $attribute = null): array
    {
        $values = ($attribute ? $this->get($attribute, []) : $this->toArray());
        if (is_object($values) || is_array($values)) {
            return json_decode(json_encode($values), true) ?: [];
        }
        return is_array($values) ? $values : [];
    }

    public function toList(mixed $attribute = null): static
    {
        $value = $attribute ? $this->get($attribute) : $this->items;
        $value = (($value instanceof static) ? $value->all() : $value);
        if (is_array($value) && empty($this->isAssoc($value))) {
            return new static($value);
        }
        return new static([$value]);
    }

    public function toArray(mixed $attribute = null): array
    {
        $values = $attribute ? $this->get($attribute) : $this->items;
        if (is_object($values) || is_array($values)) {
            return json_decode(json_encode($values), true) ?: [];
        }
        return is_array($values) ? $values : [];
    }

    public function toObject(mixed $attribute = null): static
    {
        $value = $attribute ? $this->get($attribute) : $this->items;
        $value = (($value instanceof static) ? $value->all() : $value);
        if (is_array($value) && $this->isAssoc($value)) {
            return new static($value);
        }
        if (is_array($value) && empty($this->isAssoc($value))) {
            $first = array_shift($value);
            $first = (($first instanceof static) ? $first->all() : $first);
            $first = (is_array($first) || is_object($first) ? $first : [$first]);
            return new static($first);
        }
        return new static;
    }

    public function toJson($options = 0): string
    {
        $values = $this->toArray();
        return json_encode($values, $options);
    }


    public function get(mixed $attribute, $default = null): mixed
    {
        $attribute = $this->toString($attribute);
        $indexs = array_filter(explode('.', $attribute), fn ($v) => strlen(strval($v)));
        $first = array_shift($indexs);
        $findNext = implode('.', $indexs);
        $firstExists = array_key_exists($first, $this->items);
        $valueFirst = $firstExists ?  ($this->items[$first] ?? null) : null;
        if (empty($findNext) && $firstExists) {
            return $valueFirst;
        }
        if ($firstExists && ($valueFirst instanceof static) && (strlen($findNext) > 0)) {
            return $valueFirst->get($findNext, $default);
        }
        return $this->call($default);
    }

    public function set($attribute, $value = null): static
    {
        $attribute = $this->toString($attribute);
        $indexs = array_filter(explode('.', $attribute), fn ($v) => strlen(strval($v)));
        $first = array_shift($indexs);
        $findNext = implode('.', $indexs);
        $findNext = implode('.', $indexs);
        $firstExists = array_key_exists($first, $this->items);
        $valueFirst = $firstExists ?  ($this->items[$first] ?? null) : null;
        if ($findNext && (strlen($findNext) > 0)) {
            $valueFirst = (is_array($valueFirst) ? (new static($valueFirst)) : $valueFirst);
            $valueFirst = (is_object($valueFirst) ? (new static($valueFirst)) : $valueFirst);
            $valueFirst = ($valueFirst instanceof static) ? $valueFirst : (new static());
            $this->items[$first] = $valueFirst->set($findNext, $value);
            $this->__construct($this->items);
            return $this;
        }
        $value = (is_array($value) ? (new static($value)) : $value);
        $value = (is_object($value) ? (new static($value)) : $value);
        $this->items[$first] = $value;
        $this->__construct($this->items);
        return $this;
    }

    public function exists(mixed $attribute = null): bool
    {
        $attribute = $this->toString($attribute);
        $indexs = array_filter(explode('.', $attribute), fn ($v) => strlen(strval($v)));
        $first = array_shift($indexs);
        $findNext = implode('.', $indexs);
        $firstExists = array_key_exists($first, $this->items);
        $valueFirst = $firstExists ?  ($this->items[$first] ?? null) : null;
        if ($first && $firstExists && empty($findNext)) {
            return true;
        }
        if ($firstExists && ($valueFirst instanceof static) && strlen($findNext)) {
            return $valueFirst->exists($findNext);
        }
        return false;
    }

    public function delete(string|int $attribute): static
    {
        $indexs = array_filter(explode('.', $attribute), fn ($v) => strlen(strval($v)));
        $first = array_shift($indexs);
        $findNext = implode('.', $indexs);
        $firstExists = array_key_exists($first, $this->items);
        $valueFirst = $firstExists ? ($this->items[$first] ?? null) : null;
        if ($firstExists && ($valueFirst instanceof static) && strlen($findNext)) {
            return $valueFirst->delete($findNext);
        }
        if ($firstExists && empty($findNext)) {
            unset($this->items[$first]);
            $this->__construct($this->items);
            return $this;
        }
        return $this;
    }

    public function getNivel(string|int $attribute): static
    {
        $indexs = array_filter(explode('.', $attribute), fn ($v) => strlen(strval($v)));
        $first = array_shift($indexs);
        $findNext = implode('.', $indexs);
        $firstExists = array_key_exists($first, $this->items);
        $valueFirst = $firstExists ?  ($this->items[$first] ?? null) : null;
        if (empty($findNext) && $firstExists && ($valueFirst instanceof static)) {
            return $valueFirst;
        }
        if ($findNext && $firstExists && ($valueFirst instanceof static)) {
            $valueFirst = $valueFirst->nivel($findNext);
        }
        if ($valueFirst instanceof static) {
            return $valueFirst;
        }
        return $this;
    }

    public function foreach(Closure $callback, bool $reverseKey = true): static
    {
        $values = $this->getItems();
        if (is_array($values) || is_object($values)) {
            foreach ($values as $key => $value) {
                $newValue = ($reverseKey ? $callback($value, $key) : $callback($key, $value));
                $newValue = (empty(is_null($newValue))) ? $newValue : $value;
                $values[$key] = $newValue;
            }
            $this->__construct($values);
        }
        return $this;
    }

    public function foreachRecurssive(Closure $callback, bool $reverseKey = true): static
    {
        $values = $this->getItems();
        if (is_array($values) || is_object($values)) {
            foreach ($values as $key => $value) {
                if ($value && ($value instanceof static)) {
                    $value->foreachRecurssive($callback, $reverseKey);
                    continue;
                }
                $newValue = ($reverseKey ? $callback($value, $key) : $callback($key, $value));
                $newValue = (empty(is_null($newValue))) ? $newValue : $value;
                $values[$key] = $newValue;
            }
        }
        $this->__construct($values);
        return $this;
    }

    public function foreachRecurssiveAll(Closure $callback, bool $reverseKey = true): static
    {
        $values = $this->getItems();
        if (is_array($values) || is_object($values)) {
            foreach ($values as $key => $value) {
                if ($value && ($value instanceof static)) {
                    $value->foreachRecurssive($callback, $reverseKey);
                }
                $newValue = ($reverseKey ? $callback($value, $key) : $callback($key, $value));
                $newValue = (empty(is_null($newValue))) ? $newValue : $value;
                $values[$key] = $newValue;
            }
        }
        $this->__construct($values);
        return $this;
    }

    public function foreachInCollections(Closure $callback, bool $reverseKey = true): static
    {
        $values = $this->getItems();
        if (is_array($values) || is_object($values)) {
            foreach ($values as $key => $value) {
                if ($value && ($value instanceof static)) {
                    $newValue = ($reverseKey ? $callback($value, $key) : $callback($key, $value));
                    $newValue = (empty(is_null($newValue))) ? $newValue : $value;
                    $values[$key] = $newValue;
                }
            }
        }
        $this->__construct($values);
        return $this;
    }

    public function foreachInCollectionsRecurssive(Closure $callback, bool $reverseKey = true): static
    {
        $values = $this->getItems();
        if (is_array($values) || is_object($values)) {
            foreach ($values as $key => $value) {
                if ($value && ($value instanceof static)) {
                    $value->foreachRecurssive($callback, $reverseKey);
                    $newValue = ($reverseKey ? $callback($value, $key) : $callback($key, $value));
                    $newValue = (empty(is_null($newValue))) ? $newValue : $value;
                    $values[$key] = $newValue;
                }
            }
        }
        $this->__construct($values);
        return $this;
    }

    private function toString(mixed $value = null): string
    {
        return (is_string(strval($value)) ? strval($value) : '');
    }

    private function call($value, ...$args): mixed
    {
        return ($value instanceof Closure) ? $value(...$args) : $value;
    }

    private function getCollectionRecurssive(array|object $items = []): array
    {
        foreach ($items as $key => $value) {
            $newValue = $value;
            $isDecodeOk = false;
            if (is_array($value) || is_object($value)) {
                $newValue = json_decode(json_encode($value), true);
                $isDecodeOk = json_last_error() === JSON_ERROR_NONE;
            }
            if (is_array($newValue) || is_object($newValue)) {
                if ($isDecodeOk && $newValue && empty($newValue instanceof static)) {
                    $items[$key] = new static($newValue);
                }
            }
        }
        return $this->getCollectionOrdinationObjectsInEnd($items);
    }

    private function getCollectionOrdinationObjectsInEnd(mixed $data): mixed
    {
        $isAssoc = $this->isAssoc($data);
        if (is_array($data) && empty($isAssoc)) {
            ksort($data);
            return $data;
        }
        if ((is_array($data) || is_object($data)) && $isAssoc) {
            $objects = array_filter($data, fn ($v) => is_object($v));
            $attributes = array_filter($data, fn ($v) => empty(is_object($v)));
            ksort($objects);
            ksort($attributes);
            return array_merge($attributes, $objects);
        }
        return $data;
    }

    private function isAssoc(mixed $data): bool
    {
        if ($data && (is_array($data) || is_object($data))) {
            $data = get_object_vars((object)$data);
            $keys = array_keys($data);
            $attributes = array_filter(
                $keys,
                fn ($v) => empty(is_numeric($v))
            );
            return count($attributes) > 0;
        }
        return false;
    }

    public function formatter(mixed $attribute, bool $recussive = true)
    {
        $nivel = $this->getNivel($attribute) ?: $this;
        $indexs = array_filter(explode('.', $attribute), fn ($v) => strlen(strval($v)));
        $lasted = end($indexs);
        return new CollectionFormatter($nivel, $lasted, $recussive);
    }
}
