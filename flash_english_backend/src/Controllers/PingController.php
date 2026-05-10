<?php

namespace App\Controllers;

class PingController
{
	private string $logFile = __DIR__ . "/../../public/debug.log";

	public function ping(): void
	{
		try {
			http_response_code(200);
			echo json_encode([
				"status" => "ok",
				"server_time" => date("c"),
			]);
		} catch (\Throwable $e) {
			http_response_code(401);
			echo json_encode([
				"status" => "ping error",
			]);
		}
	}
}
