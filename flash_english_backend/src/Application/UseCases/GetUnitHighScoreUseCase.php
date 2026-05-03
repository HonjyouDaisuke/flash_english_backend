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
		foreach (["category_no", "unit_no"] as $key) {
			if (!array_key_exists($key, $data)) {
				throw new \InvalidArgumentException("Missing required field: {$key}");
			}
		}

		return $this->repo->getHighScoreInfo(
			$userId,
			(int)$data["category_no"],
			(int)$data["unit_no"],
		);
	}

	public function getAll(string $userId, array $data): array
	{
		if (!array_key_exists("category_no", $data)) {
			throw new \InvalidArgumentException("Missing required field: category_no");
		}
		return $this->repo->getAllHighScoreInfo(
			$userId,
			(int)$data["category_no"],
		);
	}
}
