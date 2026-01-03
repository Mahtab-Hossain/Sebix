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
                return $this->failValidationError("Missing required field: {$f}");
            }
        }

        $role = strtolower($data['role']);
        $allowedRoles = ['end_user', 'service_provider', 'admin'];
        if (! in_array($role, $allowedRoles, true)) {
            return $this->failValidationError('Invalid role. Allowed: end_user, service_provider, admin');
        }

        $userModel = new UserModel();

        // email uniqueness
        if ($userModel->where('email', $data['email'])->first()) {
            return $this->failValidationError('Email already registered');
        }

        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
        $apiToken = bin2hex(random_bytes(32));

        $insertData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password_hash' => $passwordHash,
            'phone' => $data['phone'] ?? null,
            'location' => $data['location'] ?? null,
            'role' => $role,
            'api_token' => $apiToken,
        ];

        $id = $userModel->insert($insertData);

        if ($id === false) {
            return $this->failServerError('Unable to create user');
        }

        return $this->respondCreated([
            'id' => $id,
            'email' => $data['email'],
            'api_token' => $apiToken,
        ]);
    }

    public function login()
    {
        $data = $this->request->getJSON(true) ?? [];

        if (empty($data['email']) || empty($data['password'])) {
            return $this->failValidationError('Email and password required');
        }

        $userModel = new UserModel();
        $user = $userModel->where('email', $data['email'])->first();

        if (! $user || ! password_verify($data['password'], $user['password_hash'])) {
            return $this->fail('Invalid credentials', 401);
        }

        // ensure api_token exists
        if (empty($user['api_token'])) {
            $user['api_token'] = bin2hex(random_bytes(32));
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
