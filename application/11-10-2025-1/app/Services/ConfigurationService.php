<?php

namespace App\Services;

use App\Repositories\Contracts\ConfigurationRepositoryInterface;

class ConfigurationService {

    protected $repository;

    public function __construct(ConfigurationRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function get()
    {
        return $this->repository->getConfiguration();
    }

    public function update(array $data)
    {
        return $this->repository->updateConfiguration($data);
    }
}
