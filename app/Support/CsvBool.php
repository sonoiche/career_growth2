<?php

namespace App\Support;

final class CsvBool
{
    public static function parse(mixed $v, bool $default=false): bool
    {
        if ($v === null) return $default;

        if (is_bool($v)) return $v;
        if (is_int($v)) return $v === 1;

        $s = strtolower(trim((string)$v));
        if ($s === '') return $default;

        return in_array($s, ['1','true','t','yes','y'], true)
            || (is_numeric($s) && (int)$s === 1);
    }
}
