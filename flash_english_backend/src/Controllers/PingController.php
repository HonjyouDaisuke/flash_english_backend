<?php

namespace App\Controllers;

class PingController
{
	public function ping(): void
	{
		try {
			header('Content-Type: application/json; charset=utf-8');
			http_response_code(200);
			echo json_encode([
				"status" => "ok",
				"server_time" => date("c"),
			]);
		} catch (\Throwable $e) {
			logger()->error("Ping error: " . $e->getMessage(), ['exception' => $e]);
			header('Content-Type: application/json; charset=utf-8');
			http_response_code(500);
			echo json_encode([
				"status" => "ping error",
				"message" => $e->getMessage(),
			]);
		}
	}
}
