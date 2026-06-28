<?php

namespace App\Application\UseCases;

use App\Repositories\UnitsRepository;

class GetAllUnitsUseCase
{
	private UnitsRepository $repo;

	public function __construct(UnitsRepository $repo)
	{
		$this->repo = $repo;
	}

	public function getAll(): array
	{
		return $this->repo->getAll();
	}
}
