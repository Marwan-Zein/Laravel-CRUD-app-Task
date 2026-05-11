<?php
namespace App\Repositories\Test;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Override;

class TestUserRepository implements UserRepositoryInterface{

    /**
     * [
     *  userid => data
     * ]
     */

    private array $users = [];
    private int $userIndex = 1;

    #[Override]
    public function create(array $data): User {
        $user = new User([
            ...$data
        ]);

        $userID = $this->userIndex++;

        $this->users[$userID] = $user;
        return $user;
    }

    #[Override]
    public function findByEmail(string $email): ?User
    {
        $result = null;
        foreach($this->users as $user){
            if($user['email'] == $email) $result = $user;
        }
        return $result;
    }


}
