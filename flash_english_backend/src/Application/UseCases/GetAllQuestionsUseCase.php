<?php

namespace App\Application\UseCases;

use App\Repositories\QuestionsRepository;

class GetAllQuestionsUseCase
{
	private QuestionsRepository $repo;

	public function __construct(QuestionsRepository $repo)
	{
		$this->repo = $repo;
	}

	public function getAll(): array
	{
		return $this->repo->getAll();
	}
}
