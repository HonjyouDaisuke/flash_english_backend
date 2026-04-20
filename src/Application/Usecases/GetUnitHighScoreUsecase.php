<?php

namespace App\Application\UseCases;

use App\Repositories\UnitHighScoreRepository;
use App\Services\JwtService;

class GetUnitHighScoreUseCase
{
	private UnitHighScoreRepository $repo;

	public function __construct(UnitHighScoreRepository $repo)
	{
		$this->repo = $repo;
	}

	public function get(string $userId, array $data): array
	{
		return $this->repo->getHighScoreInfo(
			$userId,
			$data["category_id"],
			$data["unit_id"],
		);
	}

	public function getAll(string $userId, array $data): array
	{
		return $this->repo->getAllHighScoreInfo(
			$userId,
			$data["category_id"],
		);
	}
}
