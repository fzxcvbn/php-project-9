<?php

namespace App;

class UrlNormal
{
    public function normal(string $url): string
    {
        $parts = parse_url($url);

        return strtolower($parts['scheme'] . '://' . $parts['host']);
    }
}