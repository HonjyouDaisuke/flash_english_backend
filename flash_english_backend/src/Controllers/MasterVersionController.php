<?php

namespace App\Controllers;

use App\Application\UseCases\CheckMasterVersionUseCase;
use App\Application\UseCases\GetMasterVersionInfoUseCase;

class MasterVersionController
{
	private CheckMasterVersionUseCase $useCase;
	private GetMasterVersionInfoUseCase $getUseCase;

	public function __construct(CheckMasterVersionUseCase $useCase, GetMasterVersionInfoUseCase $getUseCase)
	{
		$this->useCase = $useCase;
		$this->getUseCase = $getUseCase;
	}

	public function IsNeedMasterUpdate(string $versionName, string $currentVersion): void
	{
		$input = json_decode(file_get_contents("php://input"), true);
		if (!$input) {
			http_response_code(400);
			echo json_encode(["error" => "Invalid JSON"]);
			return;
		}

		try {
			$isNeedUpdate = $this->useCase->isNeedUpdate($versionName, $currentVersion);
			http_response_code(200);
			echo json_encode(["is_need_update" => $isNeedUpdate]);
		} catch (\Exception $e) {
			http_response_code(500);
			logger()->debug("master update check error : {$e->getMessage()}");
			echo json_encode(["error" => "master update check error!"]);
		}
	}

	public function GetMasterVersionInfo(string $versionName): void
	{
		$input = json_decode(file_get_contents("php://input"), true);
		if (!$input) {
			http_response_code(400);
			echo json_encode(["error" => "Invalid JSON"]);
			return;
		}

		try {
			$versionInfo = $this->getUseCase->GetMasterVersionInfo($versionName);
			http_response_code(200);
			echo json_encode(["master_version" => $versionInfo]);
			exit();
		} catch (\Exception $e) {
			http_response_code(400);
			echo json_encode(["error" => $e->getMessage()]);
			exit();
		}
	}
}
