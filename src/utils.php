<?php
/**
 * MIT License
 *
 * Copyright (c) Jackey Cheung
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to
 * deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE
 * SOFTWARE.
 *
 */

//region Constants

/**
 * `json_encode()` options that use unicode and number check.
 */
define(
    'JSON_UNI_NUM',
    JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION | JSON_NUMERIC_CHECK
);

//endregion

/**
 * Returns the value in appropriate data type.
 *
 * Please note that non-primitive values (e.g. class) are returned as in.
 *
 * @param mixed $var
 * @return mixed
 */
function primitive_val($var)
{
    if (is_numeric($var)) {
        return numeric_val($var);
    }

    if (is_string($var) && is_yes($var)) {
        return true;
    }

    return $var;
}

//region Boolean Tests

/**
 * The inverse of `is_null()`. Handy for callback parameters.
 *
 * e.g. calling `array_map()`
 *
 * @param $val
 * @return bool
 */
function is_not_null($val): bool
{
    return null !== $val;
}

/**
 * Check if the given value may mean 'Yes'. Returns `true` if it is:
 *
 * - boolean value `true`,
 * - numeric value greater than zero (`0`)
 * - one of the string (case insensitive): `'y'`, `'yes'`, `'t'`, `'true'`,
 * `'on'`
 *
 * @param string|int|float|bool $val
 * @return bool
 */
function is_yes($val): bool
{
    if (is_bool($val)) {
        return $val;
    }

    if (is_numeric($val)) {
        return $val > 0;
    }

    if (in_array(strtolower(trim($val)), ['y', 'yes', 't', 'true', 'on'])) {
        return true;
    }

    return false;
}

/**
 * Inverse of `is_yes()`. Handy for callback parameters
 *
 * e.g. calling `array_map()`
 *
 * @param string|int|float|bool $val
 * @return bool
 */
function is_not_yes($val): bool
{
    return !is_yes($val);
}

/**
 * Check if the given value is empty. This behaves a little bit different than
 * the built-in `empty()` function. In that this returns `false` of:
 *
 * - '0' （string `'0'`）
 * - 0 （integer `0`）
 * - 0.0 （floating point `'0.0'`）
 *
 * @param mixed $val
 * @return bool
 */
function is_empty($val): bool
{
    if (is_numeric($val)) {
        return false;
    }

    if (is_string($val)) {
        return '0' !== $val && empty($val);
    }

    return empty($val);
}

/**
 * Inverse of `is_empty()`. Handy for callback parameters
 *
 * e.g. calling `array_map()`
 *
 * @param mixed $val
 * @return bool
 */
function is_not_empty($val): bool
{
    return !is_empty($val);
}

/**
 * Returns the numeric value
 *
 * @param mixed $val
 * @return int|float
 */
function numeric_val($val)
{
    if (!is_numeric($val)) {
        return 0;
    }

    return false === strpos($val, '.') ? (int)$val : (float)$val;
}

//endregion

//region String Functions

/**
 * Remove prefix from the given string
 *
 * @param string $string
 * @param string $sep delimiter
 * @return string
 */
function strip_prefix(string $string, string $sep = ':'): string
{
    $idx = strpos($string, $sep ?? ':');

    return false === $idx ? $string : substr($string, $idx + 1);
}

/**
 * Remove suffix from the given string
 *
 * @param string $string
 * @param string $sep delimiter
 * @return string
 */
function strip_suffix(string $string, string $sep = ':'): string
{
    $idx = strrpos($string, $sep ?? ':');

    return false === $idx ? $string : substr($string, 0, $idx);
}

/**
 * Concatenate given path components
 *
 * @param array ...$paths
 * @return string
 */
function path_join(...$paths): string
{
    return preg_replace(
        '#[\0\x0B\n\r/\\\\]+#',
        DIRECTORY_SEPARATOR,
        implode($paths, '/')
    );
}

/**
 * Generate a string that can be used by SQL `LIKE` operator.
 *
 * @param string $string      input string
 * @param bool   $prefix      returns a prefixed form in `text%`
 * @param bool   $suffix      returns a suffixed form in `%text`
 * @param bool   $useWildcard whether `$string` parameter contains wildcard
 * @return string
 */
function sql_like(
    string $string,
    bool $prefix = true,
    bool $suffix = false,
    bool $useWildcard = false
): string {
    if (!$useWildcard) {
        $string = str_replace(['_', '%'], ["\\_", "\\%"], $string);
    }

    if ($prefix) {
        $string .= '%';
    }

    if ($suffix) {
        $string = "%$string";
    }

    return $string;
}

//endregion

//region Numeric Functions
/**
 * Clamp the given value.
 *
 * @param int|string $value
 * @param int|string $min
 * @param int|string $max
 * @return int|string
 */
function clamp($value, $min, $max)
{
    if (null === $value) {
        return null;
    }

    return min(max($value, $min), $max);
}

//endregion

//region Array Functions

/**
 * Convert whole array to primitive_values. Keys are preserved.
 *
 * Please note that non-primitive values (e.g. class) are returned as in.
 *
 * @param array $array
 * @param bool  $recursive
 * @return array
 */
function primitive_array(array $array, $recursive = false): array
{
    if (!$recursive) {
        return array_map('primitive_val', $array);
    }

    function cvt($val)
    {
        if (is_array($val)) {
            return array_map('cvt', $val);
        }

        return primitive_val($val);
    }

    return array_map('cvt', $array);
}

/**
 * Convert CSV string to int array
 *
 * @param string $csv
 * @param bool   $keepEmpty
 *
 * @return array
 */
function csv_to_array(string $csv, bool $keepEmpty = true): array
{
    $array = array_map('primitive_val', str_getcsv($csv));

    $array = array_filter(
        $array,
        function ($val) use ($keepEmpty) {
            return !(!$keepEmpty && is_empty($val));
        }
    );

    return $array;
}

/**
 * Trim & remove empty value from the given array, recursively.
 *
 * @param mixed $array
 * @param bool  $keepEmpty
 * @return array
 */
function array_trim_filter($array, bool $keepEmpty = false): array
{
    return array_filter(
        array_map(
            function ($item) use ($keepEmpty) {
                if (is_array($item)) {
                    return array_trim_filter($item, $keepEmpty);
                }

                return is_string($item) ? trim($item) : $item;
            },
            $array
        ),
        function ($val) use ($keepEmpty) {
            return !(!$keepEmpty && is_empty($val));
        }
    );
}

/**
 * Filter out values that equal to `$needle`
 *
 * @param mixed $needle
 * @param array $haystack
 * @param bool  $invert
 * @param bool  $strict
 *
 * @return array
 */
function array_filter_value(
    $needle,
    $haystack,
    $invert = false,
    $strict = false
): array {
    return array_filter(
        $haystack,
        function ($val) use ($needle, $invert, $strict) {
            if ($invert) {
                if ($invert) {
                    return $strict ? ($val === $needle) : ($val == $needle);
                }

                return $strict ? ($val !== $needle) : ($val == $needle);
            }

            if ($invert) {
                return $strict ? ($val === $needle) : ($val != $needle);
            }

            return $strict ? ($val !== $needle) : ($val != $needle);
        }
    );
}

/**
 * Fill an associative array. Uses values of the `$keys` array to build an
 * associative array filled by `$val`.
 *
 * @param array $keys   array of keys.
 * @param mixed $val    can be a callback function
 * @param mixed $params extra parameters to be passed to the callback
 * @return array
 */
function array_fill_assoc(array $keys, $val = null, $params = null): array
{
    $array = [];
    foreach ($keys as $key) {
        $array[$key] = is_callable($val) ? $val($params) : $val;
    }//end foreach

    return $array;
}

//endregion

//region File System Functions
/**
 * Read the entire CSV file into array. See {@see fgetcsv()} for parameter
 * details.
 *
 * @param string $file Path to the file
 * @param int    $length
 * @param string $delimiter
 * @param string $enclosure
 * @param string $escape
 * @return array|bool
 */
function file_get_csv(
    $file,
    $length = 0,
    $delimiter = ',',
    $enclosure = '"',
    $escape = "\\"
) {
    $data = [];
    $ini = ini_get('auto_detect_line_endings');
    ini_set('auto_detect_line_endings', true);
    try {
        $handle = fopen($file, 'rb');
        if (false === $handle) {
            return false;
        }
        while (false !==
            $row = fgetcsv($handle, $length, $delimiter, $enclosure, $escape)) {
            $data[] = primitive_array($row);
        }
    } catch (Throwable $ex) {
        return false;
    } finally {
        ini_set('auto_detect_line_endings', $ini);
    }

    return $data;
}

/**
 * Recursively removes a folder along with all its files and directories
 *
 * @param String $path
 */
function rmdir_recursive($path)
{
    // Open the source directory to read in files
    $iterator = new DirectoryIterator($path);
    foreach ($iterator as $entry) {
        if ($entry->isFile()) {
            unlink($entry->getRealPath());
        } elseif (!$entry->isDot() && $entry->isDir()) {
            rmdir_recursive($entry->getRealPath());
        }
    }
    rmdir($path);
}

//endregion
