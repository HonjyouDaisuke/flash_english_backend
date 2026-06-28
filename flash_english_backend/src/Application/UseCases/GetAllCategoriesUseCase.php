<?php

namespace App\Application\UseCases;

use App\Repositories\CategoriesRepository;

class GetAllCategoriesUseCase
{
	private CategoriesRepository $repo;

	public function __construct(CategoriesRepository $repo)
	{
		$this->repo = $repo;
	}

	public function getAll(): ?array
	{
		return $this->repo->getAll();
	}
}
