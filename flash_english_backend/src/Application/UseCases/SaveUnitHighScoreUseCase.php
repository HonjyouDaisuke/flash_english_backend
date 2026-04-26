<?php

namespace App\Application\UseCases;

use App\Repositories\UnitHighScoreRepository;
use App\Services\JwtService;

class SaveUnitHighScoreUseCase
{
	private UnitHighScoreRepository $repo;

	public function __construct(UnitHighScoreRepository $repo)
	{
		$this->repo = $repo;
	}

	public function execute(string $userId, array $data): void
	{
		foreach (["category_id", "unit_id", "score", "achieved_at"] as $key) {
			if (!array_key_exists($key, $data)) {
				throw new \InvalidArgumentException("Missing required field: {$key}");
			}
		}
		logger()->debug('DB access start');
		$this->repo->save(
			$userId,
			(int)$data["category_id"],
			(int)$data["unit_id"],
			(int)$data["score"],
			(string)$data["achieved_at"],
		);
	}
}
