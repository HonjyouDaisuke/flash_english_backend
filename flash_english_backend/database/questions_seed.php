<?php
function runQuestionsSeed(PDO $pdo)
{
	$json = json_decode(
		file_get_contents(__DIR__ . '/seeds/questions.json'),
		true,
		512,
		JSON_THROW_ON_ERROR
	);
	echo "== Run Questions Seed count=" . count($json) . " ==\n";
	foreach ($json as $index => $question) {
		if (
			!isset($question['question_id']) ||
			$question['question_id'] === null
		) {
			echo "Broken record at index {$index}\n";
			var_dump($question);
			exit;
		}
		$stmt = $pdo->prepare("
        INSERT INTO questions (
            question_id,
            category_no,
            unit_no,
            question_no,
            japanese,
            english,
            japanese_audio_path,
            english_audio_path
        )
        VALUES (
            :question_id,
            :category_no,
            :unit_no,
            :question_no,
            :japanese,
            :english,
            :japanese_audio_path,
            :english_audio_path
        )
    ");

		$stmt->execute([
			':question_id' => $question['question_id'],
			':category_no' => $question['category_no'],
			':unit_no' => $question['unit_no'],
			':question_no' => $question['number'],
			':japanese' => $question['japanese'],
			':english' => $question['english'],
			':japanese_audio_path' => $question['japanese_audio'],
			':english_audio_path' => $question['english_audio'],
		]);
	}
}
