<?php

namespace App\Repositories;

use PDO;
use App\Config\Database;

class UserRepository
{
	private PDO $db;

	public function __construct(PDO $db)
	{
		$this->db = $db;
	}

	public function findByGoogleId(string $googleId): ?array
	{
		$stmt = $this->db->prepare("SELECT * FROM users WHERE google_id = ?");
		$stmt->execute([$googleId]);
		return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
	}

	public function create(
		string $googleId,
		string $email,
		string $name,
		string $avatar
	): string {
		$stmt = $this->db->prepare(
			"INSERT INTO users (google_id, email, name, avatar_url)
     VALUES (?, ?, ?, ?)"
		);

		$stmt->execute([$googleId, $email, $name, $avatar]);

		$user = $this->findByGoogleId($googleId);

		return $user["id"];
	}
}
