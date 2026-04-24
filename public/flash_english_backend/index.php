<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../bootstrap/logger.php';

use App\Application\UseCases\GetUnitHighScoreUseCase;
use App\Config\Database;
use App\Controllers\AuthController;
use App\Controllers\StudyLogController;
use App\Controllers\UnitHighScoresController;
use App\Repositories\UserRepository;
use App\Repositories\UnitHighScoreRepository;
use App\Application\UseCases\GoogleLoginUseCase;
use App\Application\UseCases\SaveStudyLogUseCase;
use App\Application\UseCases\SaveUnitHighScoreUseCase;
use App\Repositories\StudyLogRepository;
use App\Middleware\AuthMiddleware;

header("Content-Type: application/json");
$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$method = $_SERVER["REQUEST_METHOD"];

$uri = str_replace("/flash_english_backend", "", $uri);
// ✅ configはここだけ
$config = require __DIR__ . "/../src/config/env.local.php";

// ✅ DBはここだけ
$db = Database::connect($config);

// DI生成
// Repository
$userRepo = new UserRepository($db);
$studyLogRepo = new StudyLogRepository($db);
$unitHighScoreRepo = new UnitHighScoreRepository($db);

// Contorller
$authController = new AuthController(new GoogleLoginUseCase($userRepo));
$studyLogController = new StudyLogController(new SaveStudyLogUseCase($studyLogRepo));
$unitHighScoresController = new UnitHighScoresController(new SaveUnitHighScoreUseCase($unitHighScoreRepo), new GetUnitHighScoreUseCase($unitHighScoreRepo));

// ルーティング
$routes = [
	// 認証不要
	"POST /api/auth/google" => fn() => $authController->google(),

	// 認証必要
	"POST /api/study-log" => function () use ($studyLogController) {
		$userId = AuthMiddleware::handle();
		$studyLogController->save($userId);
	},

	"POST /api/save-unit-high-scores" => function () use ($unitHighScoresController) {
		$userId = AuthMiddleware::handle();
		$unitHighScoresController->save($userId);
	},

	"POST /api/getall-unit-high-scores" => function () use ($unitHighScoresController) {
		$userId = AuthMiddleware::handle();
		$unitHighScoresController->getAll($userId);
	},
];

$key = "$method $uri";
logger()->debug("API key = " . $key);
if (isset($routes[$key])) {
	$routes[$key]();
	exit();
}

http_response_code(404);
echo json_encode(["error" => "Not Found"]);
