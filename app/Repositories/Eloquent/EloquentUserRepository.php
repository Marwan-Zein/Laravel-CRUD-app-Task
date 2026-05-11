<?php
namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Override;

class EloquentUserRepository implements UserRepositoryInterface {

    #[Override]
    public function findByEmail(string $email): ?User {
        return User::where('email',$email)->find();
    }

    #[Override]
    public function create(array $data): User
    {
        return User::create($data);
    }

}
