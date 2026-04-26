<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Services\JwtService;

#[CoversClass(JwtService::class)]
class JwtServiceTest extends TestCase
{
	public function testGenerateAccessTokenReturnsString(): void
	{
		$token = JwtService::generateAccessToken("user123");

		$this->assertIsString($token);
		$this->assertNotEmpty($token);
	}

	public function testGenerateRefreshTokenReturnsString(): void
	{
		$token = JwtService::generateRefreshToken("user123");

		$this->assertIsString($token);
		$this->assertNotEmpty($token);
	}

	public function testVerifyReturnsUserId(): void
	{
		$token = JwtService::generateAccessToken("user123");

		$userId = JwtService::verify($token);

		$this->assertEquals("user123", $userId);
	}
}
