<?php

namespace Koru;

abstract class Middleware
{
    abstract public function handle(): bool;
}