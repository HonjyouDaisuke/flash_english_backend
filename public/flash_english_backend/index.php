<?php
require_once __DIR__ . '/../../flash_english_backend/bootstrap/app.php';
require_once BASE_PATH . '/vendor/autoload.php';
require_once BASE_PATH . '/bootstrap/logger.php';

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
logger()->debug("index.php try...");
logger()->debug('Authorization header present: ' . (isset($_SERVER["HTTP_AUTHORIZATION"]) ? 'yes' : 'no'));
$uri = str_replace("/flash_english_backend", "", $uri);

// ✅ DBはここだけ
$db = Database::connect();

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
		logger()->debug('get userId');
		$userId = AuthMiddleware::handle();
		logger()->debug('got userId = ' . $userId);
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
