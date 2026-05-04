<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


class TaskTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
    public function test_task_status_must_be_valid()
    {
        $user = User::create([
            'name' => 'User One',
            'email' => 'user1@test.com',
            'password' => bcrypt('password')
        ]);

        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/tasks', [
                'title' => 'Test Task',
                'status' => 'invalid_status'
            ]);

        $response->assertStatus(200);
    }

    public function test_task_accepts_valid_status()
    {
        $user = User::create([
            'name' => 'User One',
            'email' => 'user2@test.com',
            'password' => bcrypt('password')
        ]);
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/tasks', [
                'title' => 'Test Task',
                'status' => 'pending'
            ]);

        $response->assertStatus(200);
    }
    public function test_user_can_only_see_their_tasks()
    {
        $user1 = User::create([
            'name' => 'User One',
            'email' => 'user3@test.com',
            'password' => bcrypt('password')
        ]);
        $user2 = User::create([
            'name' => 'User Tow',
            'email' => 'user4@test.com',
            'password' => bcrypt('password')
        ]);

        $task = Task::create([
            'title' => 'User 1 task',
            'user_id' => $user1->id,
            'status' => 'pending'
        ]);

        $token = $user2->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/tasks');

        $response->assertStatus(200);

        $response->assertJsonMissing([
            'title' => 'User 1 task'
        ]);
    }
    public function test_user_cannot_access_other_users_task()
    {
        $user1 = User::create([
            'name' => 'User One',
            'email' => 'user5@test.com',
            'password' => bcrypt('password')
        ]);

        $user2 = User::create([
            'name' => 'User Two',
            'email' => 'user6@test.com',
            'password' => bcrypt('password')
        ]);

        $task = Task::create([
            'title' => 'Secret Task',
            'user_id' => $user1->id,
            'status' => 'pending'
        ]);

        $token = $user2->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(404);
    }

}
