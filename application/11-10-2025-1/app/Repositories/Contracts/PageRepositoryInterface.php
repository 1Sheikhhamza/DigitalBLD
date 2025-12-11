<?php

namespace App\Repositories\Contracts;

interface PageRepositoryInterface
{
    public function index();
    public function getAvailableParentPages($exceptId = null);
    public function getPageModule();
    public function updateSequence(int $pageId, int $newSequence): bool;
    public function create(array $data);
    public function find($id);
    public function update($id, array $data);
    public function delete($id);
    public function restore($id);
    public function forceDelete($id);
}
