<?php

namespace common;

use JsonException;

class json_loader
{
    public static function get_full_info($filename): array
    {
        $json = array();
        try {
            if (file_exists($filename)) {
                $jfc = file_get_contents($filename);
                $json = json_decode(
                    $jfc,
                    true,
                    512,
                    JSON_THROW_ON_ERROR or JSON_UNESCAPED_UNICODE);
            }
        } catch (JsonException $e) {
        }
        return $json;
    }
}