<?php

namespace App\Application\UseCases;

use App\Repositories\MasterVersionRepository;

class CheckMasterVersionUseCase
{
	private MasterVersionRepository $repo;

	public function __construct(MasterVersionRepository $repo)
	{
		$this->repo = $repo;
	}

	public function isNeedUpdate(string $versionName, string $currentVersion): bool
	{
		$latestVersion = $this->repo->get($versionName);
		$result = version_compare($latestVersion, $currentVersion, '>');
		logger()->debug("version_compare $latestVersion, $currentVersion -> $result");
		return $latestVersion !== null && version_compare($latestVersion, $currentVersion, '>');
	}
}
