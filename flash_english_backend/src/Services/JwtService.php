<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService
{
	private static string $secret = "flashenglish_super_secret_2026_dev_key_1234567890";

	// ======================
	// アクセストークン
	// ======================
	public static function generateAccessToken(string $userId): string
	{
		$payload = [
			"sub" => $userId,
			"type" => "access",
			"iat" => time(),
			"exp" => time() + 3600, // 1時間
		];

		return JWT::encode($payload, self::$secret, "HS256");
	}

	// ======================
	// リフレッシュトークン
	// ======================
	public static function generateRefreshToken(string $userId): string
	{
		$payload = [
			"sub" => $userId,
			"type" => "refresh",
			"iat" => time(),
			"exp" => time() + 60 * 60 * 24 * 7, // 7日
		];

		return JWT::encode($payload, self::$secret, "HS256");
	}

	// ======================
	// 検証
	// ======================
	public static function verify(string $token): string
	{
		return JWT::decode($token, new Key(self::$secret, "HS256"))->sub;
	}
}
