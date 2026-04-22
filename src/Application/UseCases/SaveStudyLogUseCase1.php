<?php

namespace App\Application\UseCases;

use App\Repositories\StudyLogRepository;
use App\Services\JwtService;

class SaveStudyLogUseCase
{
	private StudyLogRepository $repo;

	public function __construct(StudyLogRepository $repo)
	{
		$this->repo = $repo;
	}

	public function execute(string $userId, array $data): void
	{
		$this->repo->save(
			$userId,
			$data["category_id"],
			$data["unit_id"],
			$data["question_id"],
			$data["is_correct"],
			$data["session_id"],
			$data["duration_seconds"],
		);
	}
}
