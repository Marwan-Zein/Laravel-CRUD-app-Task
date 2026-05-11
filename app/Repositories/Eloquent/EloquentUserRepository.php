<?php
namespace App\Repositories\Eloquent;

use App\Http\Requests\CreateUserRequest;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Override;

class EloquentUserRepository implements UserRepositoryInterface {

    #[Override]
    public function findByEmail(string $email): ?User {
        $user = User::where('email',$email)->find();
        if(!$user){
            throw new ModelNotFoundException("Cannot Find User with given email");
        }
        return $user;
    }

    #[Override]
    public function create(CreateUserRequest $data): User
    {
        $validatedDAta = $data->validated();
        $validatedDAta['password'] = Hash::make($validatedDAta['password']);
        $user = User::create($validatedDAta);
        if(!$user) {
            throw new Exception("A Sql Error Occured");
        }
        return $user;
    }

}
