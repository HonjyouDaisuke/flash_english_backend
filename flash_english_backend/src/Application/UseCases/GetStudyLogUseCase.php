<?php

namespace App\Application\UseCases;

use App\Repositories\StudyLogRepository;

class GetStudyLogUseCase
{
	private StudyLogRepository $repo;

	public function __construct(StudyLogRepository $repo)
	{
		$this->repo = $repo;
	}

	public function getSince(string $userId, ?string $since): ?array
	{
		return $this->repo->getSince($userId, $since);
	}
}
