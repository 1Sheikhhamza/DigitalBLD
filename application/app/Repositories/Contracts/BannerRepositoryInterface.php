<?php

namespace App\Repositories\Contracts;

interface BannerRepositoryInterface
{
    public function index();
    public function create(array $data);
    public function find($id);
    public function update($id, array $data);
    public function delete($id);
    public function restore($id);
    public function forceDelete($id);
}
