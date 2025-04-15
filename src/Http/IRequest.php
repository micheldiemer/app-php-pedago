<?php

namespace App\Http;

interface IRequest
{
    public string $method { get; set; }
}
