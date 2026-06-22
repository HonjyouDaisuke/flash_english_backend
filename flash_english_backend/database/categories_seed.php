<?php
function runCategoriesSeed(PDO $pdo)
{
	$json = json_decode(
		file_get_contents(__DIR__ . '/seeds/categories.json'),
		true,
		512,
		JSON_THROW_ON_ERROR
	);
	echo "== Run Categories Seed count=" . count($json) . " ==\n";
	$pdo->beginTransaction();
	try {
		foreach ($json as $index => $category) {
			if (
				!isset($category['category_id']) ||
				$category['category_id'] === null ||
				!isset($category['category_no']) ||
				!isset($category['category_name']) ||
				!isset($category['category_description'])
			) {
				echo "Broken record at index {$index}: missing required fields\n";
				var_dump($category);
				$pdo->rollBack();
				exit;
			}
			$stmt = $pdo->prepare("
        INSERT INTO categories (
            category_id,
            category_no,
            category_name,
            category_description
        )
        VALUES (
            :category_id,
            :category_no,
            :category_name,
            :category_description
        )
    ");

			$stmt->execute([
				':category_id' => $category['category_id'],
				':category_no' => $category['category_no'],
				':category_name' => $category['category_name'],
				':category_description' => $category['category_description'],
			]);
		}
		$pdo->commit();
	} catch (\Exception $e) {
		$pdo->rollBack();
		throw $e;
	}
}
