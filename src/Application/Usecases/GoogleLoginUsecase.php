<?php

namespace App\Application\Usecases;

use Kreait\Firebase\Factory;
use App\Repositories\UserRepository;
use App\Services\JwtService;

class GoogleLoginUseCase
{
	private $auth;
	private UserRepository $userRepo;

	public function __construct(UserRepository $userRepo)
	{
		$this->userRepo = $userRepo;
		$f = new Factory();
		$factory = $f->withServiceAccount(
			__DIR__ . "/../../../config/firebase.json",
		);

		$this->auth = $factory->createAuth();
	}

	public function execute(string $idToken): array
	{
		// Firebase IDトークン検証
		try {
			$verifiedIdToken = $this->auth->verifyIdToken($idToken, true, 60);
		} catch (\Throwable $e) {
			file_put_contents("debug.log", "VERIFY ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
			throw $e;
		}
		$uid = $verifiedIdToken->claims()->get("sub");
		$email = $verifiedIdToken->claims()->get("email");
		$name = $verifiedIdToken->claims()->get("name") ?? "";
		$picture = $verifiedIdToken->claims()->get("picture") ?? "";

		// DB処理
		$user = $this->userRepo->findByGoogleId($uid);

		$isNew = false;

		if (!$user) {
			$userId = $this->userRepo->create($uid, $email, $name, $picture);
			$isNew = true;
		} else {
			$userId = $user["id"];
		}

		return [
			"access_token" => JwtService::generateAccessToken($userId),
			"refresh_token" => JwtService::generateRefreshToken($userId),
			"user_id" => $userId,
			"is_new" => $isNew,
		];
	}
}
