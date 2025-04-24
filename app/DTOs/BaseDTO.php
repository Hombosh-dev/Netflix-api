<?php

namespace App\DTOs;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

abstract class BaseDTO
{
    /**
     * Create a new DTO instance from request.
     *
     * @param Request $request
     * @return static
     */
    public static function fromRequest(Request $request): static
    {
        $fields = static::fields();
        $args = [];

        foreach ($fields as $key => $value) {
            $requestKey = is_string($key) ? $key : $value;
            $propertyName = is_string($key) ? $value : $key;

            // Get default value from constructor parameter
            $defaultValue = null;
            try {
                $reflection = new \ReflectionClass(static::class);
                $constructor = $reflection->getConstructor();
                if ($constructor) {
                    foreach ($constructor->getParameters() as $parameter) {
                        if ($parameter->getName() === $propertyName && $parameter->isDefaultValueAvailable()) {
                            $defaultValue = $parameter->getDefaultValue();
                            break;
                        }
                    }
                }
            } catch (\ReflectionException $e) {
                // Ignore reflection errors
            }

            $args[$propertyName] = $request->input($requestKey, $defaultValue);
        }

        return new static(...$args);
    }

    /**
     * Create a new DTO instance from array.
     *
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data): static
    {
        return new static(...Arr::only($data, static::fields()));
    }

    /**
     * Get the fields that should be used for the DTO.
     *
     * @return array
     */
    abstract public static function fields(): array;

    /**
     * Convert the DTO to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
