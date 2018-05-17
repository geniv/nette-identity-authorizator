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

        dump($this->identityAuthorizator);

//        $dirs = [
//            'temp/cache',
//            'temp/sessions',
//            'admin/temp/cache',
//            'admin/temp/sessions',
//        ];
//        $path = $this->context->parameters['wwwDir'] . '/../';
//
//        $itemCount = 0;
//        foreach ($dirs as $dir) {
//            if (file_exists($path . $dir)) {
//                foreach (Finder::find('*')->from($path . $dir) as $item) {
//                    if ($item->isFile() && unlink($item->getPathname())) {
//                        $itemCount++;
//                    }
//
//                    if ($item->isDir() && @rmdir($item->getPath())) {
//                        $itemCount++;
//                    }
//                }
//
//            }
//        }
//        $this->flashMessage('Bylo smazáno ' . $itemCount . ' položek');
//        $this->redirect('this');
    }
}

