<?php

namespace App\Repositories\Contracts;

interface UserRepositoryInterface
{
    public function index();
    public function create(array $data);
    public function find($id);
    public function update($id, array $data);
    public function deleteUser($id);
    public function restoreUser($id);
    public function forceDeleteUser($id);
}
