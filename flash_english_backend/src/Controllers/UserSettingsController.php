<?php

namespace App\Controllers;

use App\Application\UseCases\GetUserSettingUseCase;
use App\Application\UseCases\GetUserSettingsUseCase;

class UserSettingsController
{
	private GetUserSettingUseCase $getSettingUseCase;
	private GetUserSettingsUseCase $getSettingsUseCase;
	// private string $logFile = __DIR__ . "/../../public/debug.log";

	public function __construct(GetUserSettingUseCase $getSettingUseCase, GetUserSettingsUseCase $getSettingsUseCase)
	{
		$this->getSettingUseCase = $getSettingUseCase;
		$this->getSettingsUseCase = $getSettingsUseCase;
	}

	public function get(string $userId, string $settingKey): void
	{
		logger()->debug('Start user_setting_load...');

		try {
			$result = $this->getSettingUseCase->execute($userId, $settingKey);

			logger()->debug(
				'Loaded data',
				is_array($result) ? $result : ['result' => $result]
			);

			echo json_encode($result);
		} catch (\Exception $e) {
			http_response_code(400);
			echo json_encode([
				"error" => $e->getMessage()
			]);
		}
	}

	public function getAll(string $userId): void
	{
		logger()->debug('Start user_settings_load...');

		try {
			$result = $this->getSettingsUseCase->execute($userId);

			logger()->debug('Loaded data...');

			echo json_encode($result);
		} catch (\Exception $e) {
			http_response_code(500);
			echo json_encode([
				"error" => $e->getMessage()
			]);
		}
	}
}
