<?php declare(strict_types=1);

namespace Identity\Authorizator;


/**
 * Trait AuthorizatorTrait
 *
 * @author  geniv
 * @package Identity\Authorizator
 */
trait AuthorizatorTrait
{
    /** @var IIdentityAuthorizator @inject */
    public $identityAuthorizator;


    public function handleAddAcl(string $role, string $resource = null, string $privilege = null)
    {
        dump($role, $resource, $privilege);

//        dump($this->identityAuthorizator);

        $idRole = $this->identityAuthorizator->getIdRoleByName($role);
        $idResource = null;
        if ($resource) {
            $idResource = $this->identityAuthorizator->getIdResourceByName($resource);
        }

        $idPrivilege = null;
        if ($privilege) {
            $idPrivilege = $this->identityAuthorizator->getIdPrivilegeByName($privilege);
            if (!$idPrivilege) {
                $this->identityAuthorizator->savePrivilege(['id' => null, 'privilege' => $privilege]);
                $idPrivilege = $this->identityAuthorizator->getIdPrivilegeByName($privilege);
            }
        } else {
            $idPrivilege = 'all';
        }

        if ($idResource && $idPrivilege) {
            dump($this->identityAuthorizator->saveAcl($idRole, ['all' => null, $idResource => [$idPrivilege]], false));
        }

//        $this->flashMessage('Bylo smazáno ' . $itemCount . ' položek');
//        $this->redirect('this');
    }
}

