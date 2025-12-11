<?php

namespace App\Repositories\Contracts;

interface ConfigurationRepositoryInterface
{
    public function getConfiguration();
    public function updateConfiguration(array $data);
}
