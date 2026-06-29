<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

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
	public static function verify(
		string $token,
		string $expectedType = "access"
	): string {
		try {
			$decoded = JWT::decode(
				$token,
				new Key(self::$secret, "HS256")
			);

			if (($decoded->type ?? null) !== $expectedType) {
				throw new \Exception("INVALID_TOKEN_TYPE");
			}

			if (!isset($decoded->sub) || !is_string($decoded->sub) || $decoded->sub === '') {
				throw new \Exception("INVALID_SUB");
			}

			return $decoded->sub;
		} catch (ExpiredException $e) {
			http_response_code(401);
			echo json_encode([
				"error" => "TOKEN_EXPIRED"
			]);
			exit();
		} catch (\Exception $e) {
			http_response_code(401);
			echo json_encode([
				"error" => "INVALID_TOKEN"
			]);
			exit();
		}
	}
}
