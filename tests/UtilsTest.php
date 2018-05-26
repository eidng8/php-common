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

use PHPUnit\Framework\TestCase;

class UtilsTest extends TestCase
{

    public function testPrimitiveVal(): void
    {
        self::assertSame(1, primitive_val('1'));
        self::assertSame(1, primitive_val(1));
        self::assertSame(1.1, primitive_val('1.1'));
        self::assertSame(1.1, primitive_val(1.1));
        self::assertSame(['1.1', 1], primitive_val(['1.1', 1]));
    }


    public function testIsNotNull(): void
    {
        self::assertTrue(is_not_null(''));
        self::assertFalse(is_not_null(null));
    }


    public function testIsYes(): void
    {
        self::assertTrue(is_yes('y'));
        self::assertTrue(is_yes('yes'));
        self::assertTrue(is_yes('t'));
        self::assertTrue(is_yes('true'));
        self::assertTrue(is_yes('on'));
        self::assertFalse(is_yes('f'));
        self::assertFalse(is_yes(-1));
        self::assertFalse(is_yes(false));
    }


    public function testIsNotYes(): void
    {
        self::assertTrue(is_not_yes('f'));
    }


    public function testIsEmpty(): void
    {
        self::assertTrue(is_empty(''));
        self::assertTrue(is_empty(null));
        self::assertFalse(is_empty(0));
        self::assertFalse(is_empty('0'));
    }


    public function testNumericVal(): void
    {
        self::assertSame(0, numeric_val(0));
        self::assertSame(0, numeric_val('0'));
        self::assertSame(1.0, numeric_val('1.0'));
        self::assertSame(1, numeric_val('1'));
    }


    public function testStripPrefix(): void
    {
        self::assertSame('def', strip_prefix('abc:def'));
        self::assertSame('de:f', strip_prefix('abc:de:f'));
        self::assertSame('abc', strip_prefix('abc'));
    }


    public function testStripSuffix(): void
    {
        self::assertSame('abc', strip_suffix('abc:def'));
        self::assertSame('abc:de', strip_suffix('abc:de:f'));
        self::assertSame('abc', strip_suffix('abc'));
    }


    public function testPathJoin(): void
    {
        $path = implode(DIRECTORY_SEPARATOR, ['abc', 'def']);
        self::assertSame($path, path_join('abc///', '/def'));
    }


    public function testClamp(): void
    {
        self::assertSame(5, clamp(5, 3, 5));
        self::assertSame(3, clamp(3, 3, 5));
        self::assertSame(4, clamp(4, 3, 5));
        self::assertSame(5, clamp(10, 3, 5));
        self::assertSame(3, clamp(2, 3, 5));
    }


    public function testPrimitiveArray(): void
    {
        self::assertSame([1.1, 1], primitive_array(['1.1', 1]));
        self::assertSame(
            [1.1, 1, [123, 3.21]],
            primitive_array(['1.1', 1, ['123', '3.21']], true)
        );
    }


    public function testCsvToArray(): void
    {
        self::assertSame([1, 2, 3, ''], csv_to_array('1,2,3,'));
        self::assertSame([1, 2, 3], csv_to_array('1,2,3,', false));
    }


    public function testArrayTrimFilter(): void
    {
        self::assertSame(
            [0 => '1', 1 => '2', 2 => '3', 4 => [4, 5, 6]],
            array_trim_filter(['1', '2', '3', '', [4, 5, 6, '']])
        );
        self::assertSame(
            ['1', '2', '3', '', [4, 5, 6, '']],
            array_trim_filter(['1', '2', '3', '', [4, 5, 6, '']], true)
        );
    }


    public function testArrayFilterValue(): void
    {
        self::assertSame([1 => 2, 2 => 3], array_filter_value('1', [1, 2, 3]));
        self::assertSame([1], array_filter_value(1, [1, 2, 3], true));
    }


    public function testArrayFillAssoc(): void
    {
        self::assertSame(['a' => 1, 'b' => 1], array_fill_assoc(['a', 'b'], 1));
    }


    public function testFileGetCsv(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'a');
        file_put_contents($file, '1,2,3,4,5,');
        self::assertSame([[1, 2, 3, 4, 5, '']], file_get_csv($file));
        unlink($file);
    }


    public function testRmdirRecursive(): void
    {
        $dir = realpath(__DIR__ . '/..');
        $tmp = path_join($dir, 'build', 'a');
        mkdir(path_join($tmp, 'b', 'c'), 0777, true);
        rmdir_recursive($tmp);
        self::assertFileNotExists($tmp);
    }
}
