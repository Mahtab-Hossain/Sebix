<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;

class Auth extends ResourceController
{
    protected $format = 'json';

    public function register()
    {
        $data = $this->request->getJSON(true) ?? [];

        // basic required fields
        $required = ['email', 'password', 'name', 'role'];
        foreach ($required as $f) {
            if (empty($data[$f])) {
                return $this->fail("Missing required field: {$f}", 422);
            }
        }

        $role = strtolower($data['role']);
        $allowedRoles = ['end_user', 'service_provider', 'admin'];
        if (! in_array($role, $allowedRoles, true)) {
            return $this->fail('Invalid role. Allowed: end_user, service_provider, admin', 422);
        }

        $userModel = new UserModel();

        // email uniqueness
        if ($userModel->where('email', $data['email'])->first()) {
            return $this->fail('Email already registered', 422);
        }

        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
        try {
            $apiToken = bin2hex(random_bytes(32));
        } catch (\Throwable $e) {
            // fallback if random_bytes fails
            $apiToken = bin2hex(openssl_random_pseudo_bytes(32));
        }

        $insertData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password_hash' => $passwordHash,
            'phone' => $data['phone'] ?? null,
            'location' => $data['location'] ?? null,
            'role' => $role,
            'api_token' => $apiToken,
        ];

        try {
            $id = $userModel->insert($insertData);
        } catch (\Throwable $e) {
            // log and return a readable error instead of empty 500
            // ...existing error logging if you have one...
            return $this->failServerError('Unable to create user: ' . $e->getMessage());
        }

        if ($id === false) {
            return $this->failServerError('Unable to create user');
        }

        // load created user (ensure consistent data)
        $created = $userModel->find($id);

        return $this->respondCreated([
            'id' => $created['id'] ?? $id,
            'email' => $created['email'] ?? $data['email'],
            'role' => $created['role'] ?? $role,
            'api_token' => $created['api_token'] ?? $apiToken,
        ]);
    }

    public function login()
    {
        $data = $this->request->getJSON(true) ?? [];

        if (empty($data['email']) || empty($data['password'])) {
            return $this->fail('Email and password required', 422);
        }

        $userModel = new UserModel();
        $user = $userModel->where('email', $data['email'])->first();

        if (! $user || ! password_verify($data['password'], $user['password_hash'])) {
            return $this->fail('Invalid credentials', 401);
        }

        // ensure api_token exists
        if (empty($user['api_token'])) {
            try {
                $user['api_token'] = bin2hex(random_bytes(32));
            } catch (\Throwable $e) {
                $user['api_token'] = bin2hex(openssl_random_pseudo_bytes(32));
            }
            $userModel->update($user['id'], ['api_token' => $user['api_token']]);
        }

        return $this->respond([
            'id' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role'],
            'api_token' => $user['api_token'],
        ]);
    }
}
