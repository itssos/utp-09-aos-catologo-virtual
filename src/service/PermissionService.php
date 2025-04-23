<?php

declare(strict_types=1);

namespace App\Service;

use App\Models\Permission;
use Exception;

class PermissionService
{
    private Permission $permission;

    public function __construct(Permission $permission)
    {
        $this->permission = $permission;
    }

    public function getAll(): array
    {
        return $this->permission->getAll();
    }

    public function getById(int $id): array
    {
        return $this->permission->getById($id);
    }

    public function create(array $data): int
    {
        return $this->permission->create($data);
    }

    public function update(int $id, array $data): void
    {
        $ok = $this->permission->update($id, $data);
        if (!$ok) {
            throw new Exception('No se actualizó el permiso');
        }
    }

    public function delete(int $id): void
    {
        $ok = $this->permission->delete($id);
        if (!$ok) {
            throw new Exception('No se pudo eliminar el permiso');
        }
    }

    public function getByUserId(int $userId): array
    {
        return $this->permission->getByUserId($userId);
    }

    public function userHasPermission(int $userId, string $permKey): bool
    {
        $perms = array_column($this->permission->getByUserId($userId), 'name');
        return in_array($permKey, $perms, true);
    }

    public function syncUserPermissions(int $userId, array $permissionIds): void
    {
        $this->permission->syncForUser($userId, $permissionIds);
    }
}
