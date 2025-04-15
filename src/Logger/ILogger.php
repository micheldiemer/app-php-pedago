<?php

namespace App\Logger;

interface ILogger
{
    public function log(string $level, string|\Stringable $message, array $context = []);
}
