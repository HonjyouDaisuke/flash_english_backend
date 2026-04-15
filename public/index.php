<?php
require_once __DIR__ . "/../vendor/autoload.php";

use App\Controllers\AuthController;
use App\Controllers\StudyLogController;
use App\Config\Database;
use App\Repositories\UserRepository;
use App\Application\Usecases\GoogleLoginUseCase;
use App\Application\Usecases\SaveStudyLogUseCase;
use App\Repositories\StudyLogRepository;

$authHeader =
  $_SERVER["HTTP_AUTHORIZATION"] ??
  ($_SERVER["REDIRECT_HTTP_AUTHORIZATION"] ?? null);

if (!$authHeader && function_exists("apache_request_headers")) {
  $headers = apache_request_headers();
  if (isset($headers["Authorization"])) {
    $authHeader = $headers["Authorization"];
  }
}

if ($authHeader) {
  $_SERVER["HTTP_AUTHORIZATION"] = $authHeader;
}

header("Content-Type: application/json");
$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$method = $_SERVER["REQUEST_METHOD"];

$uri = str_replace("/flash_english_backend", "", $uri);
// ✅ configはここだけ
$config = require __DIR__ . "/../src/config/env.local.php";

// ✅ DBはここだけ
$db = Database::connect($config);

// ✅ 依存関係を組み立てる
$userRepo = new UserRepository($db);
$useCase = new GoogleLoginUseCase($userRepo);
$controller = new AuthController($useCase);

// ルーティング
if ($uri === "/api/auth/google" && $method === "POST") {
  $controller->google();
  exit();
}

$studyLogRepo = new StudyLogRepository($db);
$useCase = new SaveStudyLogUseCase($studyLogRepo);
$controller = new StudyLogController($useCase);
if ($uri === "/api/study-log" && $method === "POST") {
  $controller->save();
  exit();
}

if ($uri === "/api/health") {
  echo json_encode(["status" => "ok"]);
  exit();
}

http_response_code(404);
echo json_encode(["error" => "Not Found"]);
