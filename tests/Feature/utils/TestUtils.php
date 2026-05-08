<?php

use App\Repositories\Interfaces\TaskRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Spatie\Permission\Models\Role;

class TestUtils {
    private function __construct(
        private  UserRepositoryInterface $UserRepo,
        private TaskRepositoryInterface $TaskRepo
    )
    {}

    private static $instance = null;

    public static function GetInstance(){
        if(self::$instance == null){
            self::$instance = app(self::class);
        }
        return self::$instance;
    }

    private int $nextUserId = 0;

    public function createUser(array $overrides = [], ?string $role = null): User
    {
        $user = $this->UserRepo->create(array_merge([
            'name' => 'User '.$this->nextUserId,
            'email' => 'user'.$this->nextUserId.'@test.com',
            'password' => bcrypt('password123'),
        ], $overrides));

        $user->id = $this->nextUserId;
        $user->exists = true;

        $this->nextUserId++;

        if ($role !== null) {
            Role::firstOrCreate(['name' => $role]);
            $user->assignRole($role);
        }

        return $user;
    }

    public function createTask(int $userId, array $overrides = [])
    {
        return $this->TaskRepo->create($userId, array_merge([
            'title' => 'Default Task Title',
            'description' => 'Default task description',
            'status' => 'pending',
        ], $overrides));
    }
}
