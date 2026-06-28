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
use App\Repositories\CategoriesRepository;
use App\Controllers\CategoriesController;
use App\Application\UseCases\SyncUseCase;
use App\Controllers\PingController;
use App\Controllers\SyncController;
use App\Controllers\UserSettingsController;
use App\Controllers\AudioController;
use App\Repositories\StudyLogRepository;
use App\Controllers\MasterVersionController;
use App\Application\UseCases\CheckMasterVersionUseCase;
use App\Application\UseCases\GetAllCategoriesUseCase;
use App\Repositories\MasterVersionRepository;
use App\Middleware\AuthMiddleware;
use App\Controllers\UnitsController;
use App\Application\UseCases\GetAllUnitsUseCase;
use App\Repositories\UnitsRepository;
use App\Controllers\QuestionsController;
use App\Application\UseCases\GetAllQuestionsUseCase;
use App\Application\UseCases\GetMasterVersionInfoUseCase;
use App\Repositories\QuestionsRepository;

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
$categoriesRepo = new CategoriesRepository($db);
$unitsRepo = new UnitsRepository($db);
$questionsRepo = new QuestionsRepository($db);

// Contorller
$authController = new AuthController(new GoogleLoginUseCase($userRepo));
$studyLogController = new StudyLogController(new SaveStudyLogUseCase($studyLogRepo));
$unitHighScoresController = new UnitHighScoresController(new SaveUnitHighScoreUseCase($unitHighScoreRepo), new GetUnitHighScoreUseCase($unitHighScoreRepo));
$userSettingsController = new UserSettingsController(new GetUserSettingUseCase($userSettingsRepo), new GetUserSettingsUseCase($userSettingsRepo));
$pingController = new PingController();
$syncController = new SyncController(new SyncUseCase($studyLogRepo, $unitHighScoreRepo, $userSettingsRepo, $db));
$audioController = new AudioController();
$categoriesController = new CategoriesController(new GetAllCategoriesUseCase($categoriesRepo));
$unitsController = new UnitsController(new GetAllUnitsUseCase($unitsRepo));
$questionsController = new QuestionsController(new GetAllQuestionsUseCase($questionsRepo));
$masterVersionController = new MasterVersionController(new CheckMasterVersionUseCase(new MasterVersionRepository($db)), new GetMasterVersionInfoUseCase(new MasterVersionRepository($db)));

// ルーティング
$routes = [
	// 認証不要
	// ping(接続確認)
	"POST /api/ping" => fn() => $pingController->ping(),
	// googleログイン
	"POST /api/auth/google" => fn() => $authController->google(),

	"POST /api/check-master-version" => function () use ($masterVersionController) {
		$raw = json_decode(file_get_contents("php://input"), true);

		$versionName = $raw['version_name'] ?? null;
		$currentVersion = isset($raw['current_version'])
			? (string)$raw['current_version']
			: null;

		if (!is_string($versionName) || trim($versionName) === '') {
			http_response_code(400);
			echo json_encode(["error" => "versionName is required"]);
			return;
		}

		if ($currentVersion === null || trim($currentVersion) === '') {
			http_response_code(400);
			echo json_encode(["error" => "currentVersion is required"]);
			return;
		}
		logger()->debug("client currentVersion = $currentVersion");
		$masterVersionController->IsNeedMasterUpdate($versionName, $currentVersion);
	},
	"POST /api/get-master-version" => function () use ($masterVersionController) {
		$raw = json_decode(file_get_contents("php://input"), true);

		$versionName = $raw['version_name'] ?? null;
		if (!is_string($versionName) || trim($versionName) === '') {
			http_response_code(400);
			echo json_encode(["error" => "version_name is required"]);
			return;
		}
		$masterVersionController->GetMasterVersionInfo($versionName);
	},
	"POST /api/get-all-categories" => function () use ($categoriesController) {
		logger()->debug("get all categories ---");
		$categoriesController->getAll();
	},

	"POST /api/get-all-units" => function () use ($unitsController) {
		$unitsController->getAll();
	},

	"POST /api/get-all-questions" => function () use ($questionsController) {
		$questionsController->getAll();
	},
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
		$raw = json_decode(file_get_contents("php://input"), true);
		$settingKey = $raw['settingKey'] ?? $_POST['settingKey'] ?? null;

		if (!is_string($settingKey) || trim($settingKey) === '') {
			http_response_code(400);
			echo json_encode(["error" => "settingKey is required"]);
			return;
		}

		$userSettingsController->get($userId, $settingKey);
	},

	// 音声ファイルダウンロード
	"POST /api/download-audio" => function () use ($audioController) {
		$userId = AuthMiddleware::handle();
		$raw = json_decode(
			file_get_contents("php://input"),
			true
		);

		$categoryId = (int)($raw['categoryId'] ?? 0);
		$unitId = (int)($raw['unitId'] ?? 0);

		if ($categoryId <= 0 || $unitId <= 0) {
			http_response_code(400);
			echo json_encode(["error" => "categoryId and unitId are required"]);
			return;
		}

		$audioController->download(
			$categoryId,
			$unitId
		);
	},
];

$key = "$method $uri";
logger()->debug("API key = " . $key);
if (isset($routes[$key])) {
	$routes[$key]();
	exit();
}

logger()->debug("REQUEST_URI = " . $_SERVER["REQUEST_URI"]);
logger()->debug("URI = " . $uri);
logger()->debug("METHOD = " . $method);
logger()->debug("KEY = " . $key);


http_response_code(404);
echo json_encode(["error" => "Not Found"]);
