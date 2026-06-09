<?php

namespace App\Application\UseCases;

use App\Repositories\UserSettingsRepository;

class GetUserSettingUseCase
{
	public function __construct(
		private UserSettingsRepository $repository,
	) {}

	public function execute(
		string $userId,
		string $settingKey,
	): ?array {

		return $this->repository->findByKey(
			$userId,
			$settingKey,
		);
	}
}
