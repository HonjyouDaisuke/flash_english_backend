<?php

namespace App\Application\UseCases;

use App\Repositories\StudyLogRepository;

class GetQuestionStatsUseCase
{
	private StudyLogRepository $repo;

	public function __construct(StudyLogRepository $repo)
	{
		$this->repo = $repo;
	}

	public function getQuestionStats(string $userId): ?array
	{
		return $this->repo->getQuestionStats($userId);
	}
}
