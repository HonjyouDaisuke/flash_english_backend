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

	public function get(string $userId, array $data): ?array
	{
		foreach (["category_id", "unit_id"] as $key) {
			if (!array_key_exists($key, $data)) {
				throw new \InvalidArgumentException("Missing required field: {$key}");
			}
		}

		return $this->repo->getHighScoreInfo(
			$userId,
			(int)$data["category_id"],
			(int)$data["unit_id"],
		);
	}

	public function getAll(string $userId, array $data): array
	{
		if (!array_key_exists("category_id", $data)) {
			throw new \InvalidArgumentException("Missing required field: category_id");
		}
		return $this->repo->getAllHighScoreInfo(
			$userId,
			(int)$data["category_id"],
		);
	}
}
