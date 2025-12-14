<?php

namespace App\Service;

use App\Model\PinRepositoryInterface;

class PinService
{
    private PinRepositoryInterface $repository;

    public function __construct(PinRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function generateUniquePin(): string
    {
        $maxAttempts = 50;

        for ($i = 0; $i < $maxAttempts; $i++) {
            $pin = str_pad((string) random_int(1000, 9999), 4, '0', STR_PAD_LEFT);

            if (!$this->repository->findByPin($pin)) {
                return $pin;
            }
        }

        throw new \Exception("Could not generate a unique PIN after $maxAttempts attempts. Storage might be full.");
    }
}
