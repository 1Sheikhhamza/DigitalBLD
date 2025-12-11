<?php

namespace App\Services;

use App\Repositories\Contracts\PackageFeatureRepositoryInterface;

class PackageFeatureService {

    protected $repository;

    public function __construct(PackageFeatureRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index(){

        return $this->repository->index();
    }

    public function create($data){

        return $this->repository->create($data);
    }

    public function find($id){

        return $this->repository->find($id);
    }

    public function update($id, $data){

        return $this->repository->update($id, $data);
    }

    public function delete($id){

        return $this->repository->delete($id);
    }
}
