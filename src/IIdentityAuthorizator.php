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
    // define type policy
    const
        POLICY_NONE = 'none',
        POLICY_ALLOW = 'allow',
        POLICY_DENY = 'deny';

    const
        POLICY_DESCRIPTION = [
        self::POLICY_NONE  => 'all is allow, ignore part',
        self::POLICY_ALLOW => 'all is deny, allow part',
        self::POLICY_DENY  => 'all is allow, deny part',
    ];


    /**
     * Get policy.
     *
     * @return string
     */
    public function getPolicy(): string;


    /**
     * Set policy.
     *
     * @param string $policy
     */
    public function setPolicy(string $policy);


    /**
     * Get role.
     *
     * @param string|null $id
     * @return array
     */
    public function getRole(string $id = null): array;


    /**
     * Get id role by name.
     *
     * @param string $name
     * @return string
     */
    public function getIdRoleByName(string $name): string;


    /**
     * Get resource.
     *
     * @param string|null $id
     * @return array
     */
    public function getResource(string $id = null): array;


    /**
     * Get id resource by name.
     *
     * @param string $name
     * @return string
     */
    public function getIdResourceByName(string $name): string;


    /**
     * Get privilege.
     *
     * @param string|null $id
     * @return array
     */
    public function getPrivilege(string $id = null): array;


    /**
     * Get id privilege by name.
     *
     * @param string $name
     * @return string
     */
    public function getIdPrivilegeByName(string $name): string;


    /**
     * Get acl.
     *
     * @param string|null $idRole
     * @param string|null $idResource
     * @return array
     */
    public function getAcl(string $idRole = null, string $idResource = null): array;


    /**
     * Is all.
     *
     * @param string      $idRole
     * @param string|null $idResource
     * @return bool
     */
    public function isAll(string $idRole, string $idResource = null): bool;


    /**
     * Get permission.
     *
     * @return Permission
     */
    public function getPermission(): Permission;


    /**
     * Set allowed.
     *
     * @param $role
     * @param $resource
     * @param $privilege
     */
    public function setAllowed($role = self::ALL, $resource = self::ALL, $privilege = self::ALL);


    /**
     * Performs a role-based authorization.
     *
     * @param $role
     * @param $resource
     * @param $privilege
     * @return bool
     */
    public function isAllowed($role, $resource, $privilege): bool;


    /**
     * Get list current acl.
     *
     * @return array
     */
    public function getListCurrentAcl(): array;


    /**
     * Save list current acl.
     *
     * @return int
     */
    public function saveListCurrentAcl(): int;


    /**
     * Load list current acl.
     *
     * @return array
     */
    public function loadListCurrentAcl(): array;


    /**
     * Save role.
     *
     * @param array $values
     * @return int
     */
    public function saveRole(array $values): int;


    /**
     * Save resource.
     *
     * @param array $values
     * @return int
     */
    public function saveResource(array $values): int;


    /**
     * Save privilege.
     *
     * @param array $values
     * @return int
     */
    public function savePrivilege(array $values): int;


    /**
     * Save acl.
     *
     * @param string $idRole
     * @param array  $values
     * @param bool   $deleteBeforeSave
     * @return int
     */
    public function saveAcl(string $idRole, array $values, bool $deleteBeforeSave = true): int;
}
