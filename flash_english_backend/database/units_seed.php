<?php
function runUnitsSeed(PDO $pdo)
{
	$json = json_decode(
		file_get_contents(__DIR__ . '/seeds/units.json'),
		true,
		512,
		JSON_THROW_ON_ERROR
	);
	echo "== Run Units Seed count=" . count($json) . " ==\n";
	$pdo->beginTransaction();
	try {
		$stmt = $pdo->prepare("
      INSERT INTO units (
        unit_id,
        category_no,
		    unit_no,
        unit_name,
        unit_description
      )
      VALUES (
        :unit_id,
        :category_no,
        :unit_no,
        :unit_name,
        :unit_description
  	  )
    ");
		foreach ($json as $index => $unit) {
			if (
				!isset($unit['unit_id']) ||
				$unit['unit_id'] === null ||
				!isset($unit['category_no']) ||
				!isset($unit['unit_no']) ||
				!isset($unit['unit_name']) ||
				!isset($unit['unit_description'])
			) {
				echo "Broken record at index {$index}: missing required fields\n";
				var_dump($unit);
				$pdo->rollBack();
				exit;
			}


			$stmt->execute([
				':unit_id' => $unit['unit_id'],
				':category_no' => $unit['category_no'],
				':unit_no' => $unit['unit_no'],
				':unit_name' => $unit['unit_name'],
				':unit_description' => $unit['unit_description'],
			]);
		}
		$pdo->commit();
	} catch (\Exception $e) {
		$pdo->rollBack();
		throw $e;
	}
}
