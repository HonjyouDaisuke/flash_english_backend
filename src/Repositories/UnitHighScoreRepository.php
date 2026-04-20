<?php

namespace App\Repositories;

use PDO;

class UnitHighScoreRepository
{
	private PDO $pdo;

	public function __construct(PDO $pdo)
	{
		$this->pdo = $pdo;
	}

	public function getHighScore(string $userId, int $categoryId, int $unitId): ?int
	{
		$sql = file_get_contents(__DIR__ . "/sql/select_unit_high_score.sql");
		$stmt = $this->pdo->prepare($sql);
		if (!$stmt) {
			return null;
		}
		$stmt->execute([
			":user_id" => $userId,
			":category_id" => $categoryId,
			":unit_id" => $unitId,
		]);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result ? (int)$result["high_score"] : null;
	}

	public function getHighScoreInfo(string $userId, int $categoryId, int $unitId): ?array
	{
		$sql = file_get_contents(__DIR__ . "/sql/select_unit_high_score.sql");
		$stmt = $this->pdo->prepare($sql);
		if (!$stmt) {
			return null;
		}
		$stmt->execute([
			":user_id" => $userId,
			":category_id" => $categoryId,
			":unit_id" => $unitId,
		]);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result;
	}

	public function getAllHighScoreInfo(string $userId, int $categoryId): array
	{
		$sql = file_get_contents(__DIR__ . "/sql/select_all_unit_high_score.sql");
		$stmt = $this->pdo->prepare($sql);
		file_put_contents("debug.log", "fetch units score " . $sql . ":u-" . $userId . ":c-" . $categoryId . "\n", FILE_APPEND);
		if (!$stmt) {
			return [];
		}
		$stmt->execute([
			":user_id" => $userId,
			":category_id" => $categoryId,
		]);
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		file_put_contents("debug.log", print_r($result, true), FILE_APPEND);
		file_put_contents("debug.log", "-------------\n" . $sql . ":u-" . $userId . ":c-" . $categoryId . "\n", FILE_APPEND);
		return $result;
	}

	public function _checkHighScore(string $userId, int $categoryId, int $unitId, int $newScore): bool
	{
		$highScore = $this->getHighScore($userId, $categoryId, $unitId);
		return $newScore > $highScore;
	}

	public function save(string $userId, int $categoryId, int $unitId, int $newScore, string $achievedAt): bool
	{
		if ($this->getHighScore($userId, $categoryId, $unitId) === null) {
			// [INSERT] ハイスコアが存在しない場合は新規作成
			file_put_contents("debug.log", "FileSave Insert\n", FILE_APPEND);
			$sql = file_get_contents(__DIR__ . "/sql/insert_unit_high_score.sql");
			$stmt = $this->pdo->prepare($sql);
			if (!$stmt) {
				return false;
			}
			$result = $stmt->execute([
				":user_id" => $userId,
				":category_id" => $categoryId,
				":unit_id" => $unitId,
				":high_score" => $newScore,
				":achieved_at" => $achievedAt,
			]);
			return $result;
		} elseif (!$this->_checkHighScore($userId, $categoryId, $unitId, $newScore)) {
			file_put_contents("debug.log", "FileSave Nothing\n", FILE_APPEND);

			// [NOTHING] ハイスコアが存在し、かつ新しいスコアがハイスコアを超えていない場合は更新しない
			return false;
		} else {
			// [UPDATE] ハイスコアが存在し、かつ新しいスコアがハイスコアを超えている場合は更新する
			file_put_contents("debug.log", "FileSave Update\n", FILE_APPEND);
			$sql = file_get_contents(__DIR__ . "/sql/update_unit_high_score.sql");
			$stmt = $this->pdo->prepare($sql);
			if (!$stmt) {
				return false;
			}
			$result = $stmt->execute([
				":high_score" => $newScore,
				":user_id" => $userId,
				":category_id" => $categoryId,
				":unit_id" => $unitId,
				":achieved_at" => $achievedAt,
			]);
			return $result;
		}
	}
}
