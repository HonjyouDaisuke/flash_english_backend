<?php

namespace App\Domain\Entities;

class User
{
  public function __construct(
    public string $id,
    public string $name = "",
    public string $email = "",
    public string $google_id = "",
    public ?string $avater_url = "",
  ) {}
}
