<?php

namespace App\Application\UseCases;

use App\Repositories\UserSettingsRepository;

class GetUserSettingsUseCase
{
	public function __construct(
		private UserSettingsRepository $repository,
	) {}

	public function execute(
		string $userId,
	): array {

		return $this->repository->findAll(
			$userId,
		);
	}
}
