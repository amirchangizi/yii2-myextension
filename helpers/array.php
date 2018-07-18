<?php
    /*
    | Author : Ata amini
    | Email  : ata.aminie@gmail.com
    | Date   : 2018-04-20
    | TIME   : 10:36 AM
    */

    if (!function_exists('array_extract')) {
        /**
         * extract a key from an array
         *
         * @param      $key
         * @param      $array
         * @param null $default
         *
         * @return mixed|null
         */
        function smart_extract($key, $array, $default = null)
        {
            if (!is_array($array))
                $array = (array)$array;

            if (array_key_exists($key, $array)) {
                return $array[$key];
            }
            if (strpos($key, '.') === false) {
                return $default;
            }
            $items = $array;
            foreach (explode('.', $key) as $segment) {
                if (!is_array($items) || !array_key_exists($segment, $items)) {
                    return $default;
                }
                $items = &$items[$segment];
            }
            return $items;
        }
    }

    if (!function_exists('is_empty')) {
        /**
         * Check if a variable is empty or not set
         *
         * @param string|array|null $var
         *
         * @return bool
         */
        function is_empty($var)
        {
            return !isset($var) ? true : (is_array($var) ? empty($var) : ($var === null || $var === ''));
        }
    }

    if (!function_exists('array_accessible')) {
        /**
         * determine whether the given value is array accessible
         *
         * @param  mixed $value
         *
         * @return bool
         */
        function array_accessible($value)
        {
            return is_array($value) || $value instanceof ArrayAccess;
        }
    }

    if (!function_exists('array_get')) {
        /**
         * get an item from an array using "dot" notation
         *
         * @param  \ArrayAccess|array $array
         * @param  string             $key
         * @param  mixed              $default
         *
         * @return mixed
         */
        function array_get($array, $key, $default = null)
        {
            if (!array_accessible($array)) {
                return ensure_value($default);
            }

            if (is_null($key)) {
                return $array;
            }

            if (array_exists($array, $key)) {
                return $array[$key];
            }

            foreach (explode('.', $key) as $segment) {
                if (array_accessible($array) && array_exists($array, $segment)) {
                    $array = $array[$segment];
                } else {
                    return ensure_value($default);
                }
            }

            return $array;
        }
    }

    if (!function_exists('array_exists')) {
        /**
         * determine if the given key exists in the provided array
         *
         * @param  \ArrayAccess|array $array
         * @param  string|int         $key
         *
         * @return bool
         */
        function array_exists($array, $key)
        {
            if ($array instanceof ArrayAccess) {
                return $array->offsetExists($key);
            }

            return array_key_exists($key, $array);
        }
    }

    if (!function_exists('array_first')) {
        /**
         * return the first element in an array passing a given truth test
         *
         * @param  array         $array
         * @param  callable|null $callback
         * @param  mixed         $default
         *
         * @return mixed
         */
        function array_first($array, callable $callback = null, $default = null)
        {
            if (is_null($callback)) {
                if (empty($array))
                    return ensure_value($default);

                foreach ($array as $item) {
                    return $item;
                }
            }

            foreach ($array as $key => $value) {
                if (call_user_func($callback, $value, $key)) {
                    return $value;
                }
            }

            return ensure_value($default);
        }
    }


    if (!function_exists('array_last')) {
        /**
         * return the last element in an array passing a given truth test
         *
         * @param  array         $array
         * @param  callable|null $callback
         * @param  mixed         $default
         *
         * @return mixed
         */
        function array_last($array, callable $callback = null, $default = null)
        {
            if (is_null($callback)) {
                return empty($array) ? ensure_value($default) : end($array);
            }

            return array_first(array_reverse($array, true), $callback, $default);
        }
    }

    if (!function_exists('array_forget')) {
        /**
         * remove one or many array items from a given array using "dot" notation
         *
         * @param $array
         * @param $keys
         */
        function array_forget(&$array, $keys)
        {
            $original = &$array;
            $keys = (array)$keys;
            if (count($keys) === 0)
                return;

            foreach ($keys as $key) {
                // if the exact key exists in the top-level, remove it
                if (array_exists($array, $key)) {
                    unset($array[$key]);
                    continue;
                }

                $parts = explode('.', $key);
                $array = &$original;
                while (count($parts) > 1) {
                    $part = array_shift($parts);

                    if (isset($array[$part]) && is_array($array[$part])) {
                        $array = &$array[$part];
                    } else {
                        continue 2;
                    }
                }
                unset($array[array_shift($parts)]);
            }
        }
    }

    if (!function_exists('array_except')) {
        /**
         * get all of the given array except for a specified array of items
         *
         * @param  array        $array
         * @param  array|string $keys
         *
         * @return array
         */
        function array_except($array, $keys)
        {
            array_forget($array, $keys);
            return $array;
        }
    }

    if (!function_exists('array_is_assoc')) {
        /**
         * determines if an array is associative.
         * an array is "associative" if it doesn't have sequential numerical keys beginning with zero.
         *
         * @param  array $array
         *
         * @return bool
         */
        function array_is_assoc(array $array)
        {
            $keys = array_keys($array);
            return array_keys($keys) !== $keys;
        }
    }

    if (!function_exists('array_prepend')) {
        /**
         * push an item onto the beginning of an array.
         *
         * @param  array $array
         * @param  mixed $value
         * @param  mixed $key
         *
         * @return array
         */
        function array_prepend($array, $value, $key = null)
        {
            if (is_null($key)) {
                array_unshift($array, $value);
            } else {
                $array = [$key => $value] + $array;
            }
            return $array;
        }
    }


    if (!function_exists('array_where')) {
        /**
         * filter the array using the given callback
         *
         * @param  array    $array
         * @param  callable $callback
         *
         * @return array
         */
        function array_where($array, callable $callback)
        {
            return array_filter($array, $callback, ARRAY_FILTER_USE_BOTH);
        }
    }

    if (!function_exists('ensure_array')) {
        /**
         * if the given value is not an array, wrap it in one
         *
         * @param  mixed $value
         *
         * @return array
         */
        function ensure_array($value)
        {
            return !is_array($value) ? [$value] : $value;
        }
    }


    if (!function_exists('ensure_value')) {
        /**
         * return the default value of the given value
         *
         * @param  mixed $value
         *
         * @return mixed
         */
        function ensure_value($value)
        {
            return $value instanceof Closure ? $value() : $value;
        }
    }

    if (!function_exists('array_sort_multidimensional')) {
        /**
         * ordering array
         *
         * @param array $array
         * @param       $sortField
         * @param int   $sortFlags
         *
         * @return bool
         */
        function array_sort_multidimensional(array &$array, $sortField, $sortFlags = SORT_ASC)
        {
            $temp = \array_column($array, $sortField);
            \array_multisort($temp, $sortFlags, $array);
        }
    }

    IF (!function_exists('array_merge_recursive_distinct')) {
        /**
         * array_merge_recursive does indeed merge arrays, but it converts values with duplicate
         * keys to arrays rather than overwriting the value in the first array with the duplicate
         * value in the second array, as array_merge does. I.e.
         *
         * @param array $array1
         * @param array $array2
         *
         * @return array
         */
        function array_merge_recursive_distinct(array &$array1, array &$array2)
        {
            $merged = $array1;
            foreach ($array2 as $key => &$value) {
                if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                    $merged[$key] = array_merge_recursive_distinct($merged[$key], $value);
                } else {
                    $merged[$key] = $value;
                }
            }
            return $merged;
        }
    }