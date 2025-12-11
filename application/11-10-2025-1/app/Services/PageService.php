<?php

namespace App\Services;

use App\Repositories\Contracts\PageRepositoryInterface;

class PageService
{

    protected $repository;

    public function __construct(PageRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {

        return $this->repository->index();
    }

    public function getAvailableParentPages($exceptId = null)
    {
        return $this->repository->getAvailableParentPages($exceptId);
    }

    public function getPageModule()
    {
        return $this->repository->getPageModule();
    }

    public function updateSequence(int $pageId, int $newSequence): bool
    {
        return $this->repository->updateSequence($pageId, $newSequence);
    }



    public function create($data)
    {

        return $this->repository->create($data);
    }

    public function find($id)
    {

        return $this->repository->find($id);
    }

    public function update($id, $data)
    {

        return $this->repository->update($id, $data);
    }

    public function delete($id)
    {

        return $this->repository->delete($id);
    }

    public function restore($id)
    {

        return $this->repository->restore($id);
    }

    public function forceDelete($id)
    {

        return $this->repository->forceDelete($id);
    }
}
