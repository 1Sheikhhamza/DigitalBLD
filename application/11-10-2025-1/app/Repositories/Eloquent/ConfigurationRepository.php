<?php

namespace App\Repositories\Eloquent;

use App\Models\Configuration;
use App\Repositories\Contracts\ConfigurationRepositoryInterface;

class ConfigurationRepository implements ConfigurationRepositoryInterface
{
    public function getConfiguration()
    {
        return Configuration::first();
    }

    public function updateConfiguration(array $data)
    {
        $config = Configuration::first() ?? new Configuration();
        $config->fill($data);
        $config->save();

        return $config;
    }
}
