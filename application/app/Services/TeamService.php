<?php

namespace App\Services;

use App\Repositories\Contracts\TeamRepositoryInterface;

class TeamService {

    protected $repository;

    public function __construct(TeamRepositoryInterface $repository)
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

    public function restore($id){

        return $this->repository->restore($id);
    }

    public function forceDelete($id){

        return $this->repository->forceDelete($id);
    }
}
