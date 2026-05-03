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

	public function getHighScore(string $userId, int $categoryNo, int $unitNo): ?int
	{
		$sql = file_get_contents(__DIR__ . "/sql/select_unit_high_score.sql");
		$stmt = $this->pdo->prepare($sql);
		if (!$stmt) {
			return null;
		}
		$stmt->execute([
			":user_id" => $userId,
			":category_no" => $categoryNo,
			":unit_no" => $unitNo,
		]);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result ? (int)$result["high_score"] : null;
	}

	public function getHighScoreInfo(string $userId, int $categoryNo, int $unitNo): ?array
	{
		$sql = file_get_contents(__DIR__ . "/sql/select_unit_high_score.sql");
		$stmt = $this->pdo->prepare($sql);
		if (!$stmt) {
			return null;
		}
		$stmt->execute([
			":user_id" => $userId,
			":category_no" => $categoryNo,
			":unit_no" => $unitNo,
		]);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result !== false ? $result : null;
	}

	public function getAllHighScoreInfo(string $userId, int $categoryNo): array
	{
		$sql = file_get_contents(__DIR__ . "/sql/select_all_unit_high_score.sql");
		$stmt = $this->pdo->prepare($sql);
		logger()->debug("fetch units score " . $sql . ":u-" . $userId . ":c-" . $categoryNo);
		if (!$stmt) {
			return [];
		}
		$stmt->execute([
			":user_id" => $userId,
			":category_no" => $categoryNo
		]);
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		logger()->debug(print_r($result, true));
		logger()->debug('-------------');
		return $result;
	}

	public function _checkHighScore(string $userId, int $categoryNo, int $unitNo, int $newScore): bool
	{
		$highScore = $this->getHighScore($userId, $categoryNo, $unitNo);
		return $newScore > $highScore;
	}

	public function save(string $userId, int $categoryNo, int $unitNo, int $newScore, string $achievedAt): bool
	{
		if ($this->getHighScore($userId, $categoryNo, $unitNo) === null) {
			// [INSERT] ハイスコアが存在しない場合は新規作成
			logger()->debug('FileSave Insert');
			$sql = file_get_contents(__DIR__ . "/sql/insert_unit_high_score.sql");
			$stmt = $this->pdo->prepare($sql);
			if (!$stmt) {
				return false;
			}
			$result = $stmt->execute([
				":user_id" => $userId,
				":category_no" => $categoryNo,
				":unit_no" => $unitNo,
				":high_score" => $newScore,
				":achieved_at" => $achievedAt,
			]);
			return $result;
		} elseif (!$this->_checkHighScore($userId, $categoryNo, $unitNo, $newScore)) {
			logger()->debug('FileSave Nothing');

			// [NOTHING] ハイスコアが存在し、かつ新しいスコアがハイスコアを超えていない場合は更新しない
			return false;
		} else {
			// [UPDATE] ハイスコアが存在し、かつ新しいスコアがハイスコアを超えている場合は更新する
			logger()->debug('FileSave Update');
			$sql = file_get_contents(__DIR__ . "/sql/update_unit_high_score.sql");
			$stmt = $this->pdo->prepare($sql);
			if (!$stmt) {
				return false;
			}
			$result = $stmt->execute([
				":high_score" => $newScore,
				":user_id" => $userId,
				":category_no" => $categoryNo,
				":unit_no" => $unitNo,
				":achieved_at" => $achievedAt,
			]);
			return $result;
		}
	}
}
