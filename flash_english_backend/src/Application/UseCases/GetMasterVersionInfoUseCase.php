<?php

namespace App\Application\UseCases;

use App\Repositories\MasterVersionRepository;

class GetMasterVersionInfoUseCase
{
	private MasterVersionRepository $repo;

	public function __construct(MasterVersionRepository $repo)
	{
		$this->repo = $repo;
	}

	public function GetMasterVersionInfo(string $versionName): ?array
	{
		$versionInfo = $this->repo->getVersionInfo($versionName);
		return $versionInfo;
	}
}
