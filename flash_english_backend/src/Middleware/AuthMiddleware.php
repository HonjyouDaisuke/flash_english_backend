<?php

namespace App\Middleware;

use App\Services\JwtService;

class AuthMiddleware
{
	public static function handle(): string
	{
		logger()->debug('AuthMiddleware start');
		$headers = getallheaders() ?: [];
		foreach (['Authorization', 'Cookie', 'Set-Cookie', 'Proxy-Authorization'] as $h) {
			foreach ($headers as $k => $v) {
				if (strcasecmp($k, $h) === 0) {
					$headers[$k] = '***REDACTED***';
				}
			}
		}
		logger()->debug('headers', $headers);

		$authHeader =
			$_SERVER["HTTP_AUTHORIZATION"] ??
			($_SERVER["REDIRECT_HTTP_AUTHORIZATION"] ?? null);

		if (!$authHeader && function_exists("apache_request_headers")) {
			$headers = apache_request_headers();
			if (isset($headers["Authorization"])) {
				$authHeader = $headers["Authorization"];
			}
		}

		if (!$authHeader) {
			http_response_code(401);
			echo json_encode(["error" => "Unauthorized"]);
			exit();
		}

		if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
			$token = $matches[1];

			// 👇ここが本質
			$userId = JwtService::verify($token);

			return $userId;
		}

		http_response_code(401);
		echo json_encode(["error" => "Invalid token"]);
		exit();
	}
}
