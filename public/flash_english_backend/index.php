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
use App\Application\UseCases\GetUserSettingUseCase;
use App\Application\UseCases\GetUserSettingsUseCase;
use App\Repositories\UserSettingsRepository;
use App\Application\UseCases\SyncUseCase;
use App\Controllers\PingController;
use App\Controllers\SyncController;
use App\Controllers\UserSettingsController;
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
$userSettingsRepo = new UserSettingsRepository($db);

// Contorller
$authController = new AuthController(new GoogleLoginUseCase($userRepo));
$studyLogController = new StudyLogController(new SaveStudyLogUseCase($studyLogRepo));
$unitHighScoresController = new UnitHighScoresController(new SaveUnitHighScoreUseCase($unitHighScoreRepo), new GetUnitHighScoreUseCase($unitHighScoreRepo));
$userSettingsController = new UserSettingsController(new GetUserSettingUseCase($userSettingsRepo), new GetUserSettingsUseCase($userSettingsRepo));
$pingController = new PingController();
$syncController = new SyncController(new SyncUseCase($studyLogRepo, $unitHighScoreRepo, $userSettingsRepo, $db));
// ルーティング
$routes = [
	// 認証不要
	// ping(接続確認)
	"POST /api/ping" => fn() => $pingController->ping(),
	// googleログイン
	"POST /api/auth/google" => fn() => $authController->google(),

	// 認証必要
	"POST /api/sync" => function () use ($syncController) {
		$userId = AuthMiddleware::handle();
		$syncController->sync($userId);
	},

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
		logger()->debug('getAll Unit High Scores userId = ' . $userId);
		$unitHighScoresController->getAll($userId);
	},

	"POST /api/getall-user-settings" => function () use ($userSettingsController) {
		$userId = AuthMiddleware::handle();
		logger()->debug('getAll User Settings userId = ' . $userId);
		$userSettingsController->getAll($userId);
	},

	"POST /api/get-user-settings" => function () use ($userSettingsController) {
		$userId = AuthMiddleware::handle();
		logger()->debug('get User Settings userId = ' . $userId);
		$userSettingsController->get($userId, $_POST['settingKey']);
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
