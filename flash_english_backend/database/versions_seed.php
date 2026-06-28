<?php
function runVersionsSeed(PDO $pdo)
{
	$json = json_decode(
		file_get_contents(__DIR__ . '/seeds/master_versions.json'),
		true,
		512,
		JSON_THROW_ON_ERROR
	);
	echo "== Run Versions Seed count=" . count($json) . " ==\n";
	$pdo->beginTransaction();
	try {
		$stmt = $pdo->prepare("
      REPLACE INTO master_version (
        version_id,
        version_no,
        version_name,
        version_description
      )
      VALUES (
        :version_id,
        :version_no,
        :version_name,
        :version_description
  	  )
    ");
		foreach ($json as $index => $version) {
			if (
				!isset($version['version_id']) ||
				$version['version_id'] === null ||
				!isset($version['version_no']) ||
				!isset($version['version_name']) ||
				!isset($version['version_description'])
			) {
				throw new RuntimeException(
					"Broken version record at index {$index}: missing required fields"
				);
			}


			$stmt->execute([
				':version_id' => $version['version_id'],
				':version_no' => $version['version_no'],
				':version_name' => $version['version_name'],
				':version_description' => $version['version_description'],
			]);
		}
		$pdo->commit();
	} catch (\Exception $e) {
		$pdo->rollBack();
		throw $e;
	}
}
