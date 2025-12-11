<?php

namespace App\Repositories\Contracts;

interface PackageFeatureRepositoryInterface
{
    public function index();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}
