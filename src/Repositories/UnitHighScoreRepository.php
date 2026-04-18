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

	public function _checkHighScore(string $userId, int $categoryId, int $unitId, int $newScore): bool
	{
		$highScore = $this->getHighScore($userId, $categoryId, $unitId);
		return $newScore > $highScore;
	}

	public function save(string $userId, int $categoryId, int $unitId, int $newScore): bool
	{
		if ($this->getHighScore($userId, $categoryId, $unitId) === null) {
			// [INSERT] ハイスコアが存在しない場合は新規作成
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
			]);
			return $result;
		} elseif (!$this->_checkHighScore($userId, $categoryId, $unitId, $newScore)) {
			// [NOTHING] ハイスコアが存在し、かつ新しいスコアがハイスコアを超えていない場合は更新しない
			return false;
		} else {
			// [UPDATE] ハイスコアが存在し、かつ新しいスコアがハイスコアを超えている場合は更新する
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
			]);
			return $result;
		}
	}
}
