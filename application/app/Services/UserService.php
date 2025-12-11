<?php

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryInterface;

class UserService {

    protected $repository;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index(){

        return $this->repository->index();
    }

    public function store($data){

        return $this->repository->create($data);
    }

    public function find($id){

        return $this->repository->find($id);
    }

    public function update($id, $data){

        return $this->repository->update($id, $data);
    }

    public function deleteUser($id){

        return $this->repository->deleteUser($id);
    }

    public function restoreUser($id){

        return $this->repository->restoreUser($id);
    }

    public function forceDeleteUser($id){

        return $this->repository->forceDeleteUser($id);
    }
}
