<?php

namespace App\Repositories\Interfaces;

use App\Http\Requests\CreateUserRequest;
use App\Models\User;

interface UserRepositoryInterface
{
    public function create(CreateUserRequest $data): User;
    public function findByEmail(string $email): ?User;

}
