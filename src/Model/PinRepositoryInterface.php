<?php

namespace App\Model;

interface PinRepositoryInterface
{
    public function findByPin(string $pin): ?array;
}
