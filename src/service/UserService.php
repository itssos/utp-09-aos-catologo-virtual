<?php

declare(strict_types=1);

namespace App\Service;

use App\Models\User;
use Exception;

class UserService
{
    private User $user;
    private ?PermissionService $permService;

    public function __construct(User $user, ?PermissionService $permService = null)
    {
        $this->user = $user;
        $this->permService = $permService;
    }

    public function getAll(): array
    {
        return $this->user->getAll();
    }

    public function getById(int $id): User
    {
        return $this->user->getById($id);
    }

    public function getByUsername(string $username): User
    {
        return $this->user->findByUsername($username);
    }

    public function create(array $data): int
    {
        return $this->user->create($data);
    }

    public function update(int $id, array $data): void
    {
        $ok = $this->user->update($id, $data);
        if (!$ok) {
            throw new Exception('No se actualizó el usuario');
        }
    }

    public function updateWithPermissions(int $id, array $data, ?array $permissionIds = null): void
    {
        $this->update($id, $data);
        if ($permissionIds !== null) {
            $this->permService->syncUserPermissions($id, $permissionIds);
        }
    }

    public function delete(int $id): void
    {
        $ok = $this->user->delete($id);
        if (!$ok) {
            throw new Exception('No se pudo eliminar el usuario');
        }
    }
}
