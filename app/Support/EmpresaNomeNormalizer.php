<?php

namespace App\Support;

class EmpresaNomeNormalizer
{
    public static function normalize(string $s): string
    {
        $s = trim(mb_strtolower($s, 'UTF-8'));
        if (class_exists(\Normalizer::class)) {
            $s = \Normalizer::normalize($s, \Normalizer::FORM_D);
            $s = preg_replace('/\pM/u', '', $s) ?? $s;
        }
        $trans = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
        if ($trans !== false) {
            $s = $trans;
        }
        $s = mb_strtolower($s, 'UTF-8');
        $s = preg_replace('/[^a-z0-9\s]/', ' ', $s) ?? $s;
        $s = preg_replace('/\s+/', ' ', $s) ?? $s;

        return trim($s);
    }
}
