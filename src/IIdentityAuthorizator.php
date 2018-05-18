<?php declare(strict_types=1);

namespace Identity\Authorizator;

use Nette\Security\IAuthorizator;
use Nette\Security\Permission;


/**
 * Interface IIdentityAuthorizator
 *
 * @author  geniv
 * @package Identity\Authorizator
 */
interface IIdentityAuthorizator extends IAuthorizator
{
    public function getPolicy(): string;


    public function setPolicy(string $policy);


    public function getRole(string $id = null): array;


    public function getIdRoleByName(string $name): string;


    public function getResource($id = null): array;


    public function getIdResourceByName(string $name): string;


    public function getPrivilege($id = null): array;


    public function getIdPrivilegeByName(string $name): string;


    public function getAcl($idRole = null, $idResource = null): array;


    public function isAll($idRole, $idResource = null): bool;


    public function getPermission(): Permission;


    public function setAllowed($role = self::ALL, $resource = self::ALL, $privilege = self::ALL);


    public function isAllowed($role, $resource, $privilege): bool;


    public function getListCurrentAcl(): array;


    public function saveRole(array $values): int;


    public function saveResource(array $values): int;


    public function savePrivilege(array $values): int;


    public function saveAcl(string $idRole, array $values, bool $deleteBeforeSave = true): int;
}
