<?php declare(strict_types=1);

namespace Identity\Authorizator\Drivers;

use Nette\DI\Container;
use Nette\Neon\Neon;


/**
 * Class NeonDriver
 *
 * @author  geniv
 * @package Identity\Authorizator\Drivers
 */
class NeonDriver extends ArrayDriver
{
    /** @var string */
    private $path = null;
    /** @var array */
    private $data = null;


    /**
     * NeonDriver constructor.
     *
     * @param string    $path
     * @param Container $container
     */
    public function __construct(string $path, Container $container)
    {
        $this->setPath($path);

        if ($this->path && file_exists($this->path)) {
            $this->data = Neon::decode(file_get_contents($this->path));

            parent::__construct($this->data['role'], $this->data['resource'], $this->data['privilege'], $this->data['acl'], $container);
        }
    }


    /**
     * Set path.
     *
     * @param string $path
     * @return NeonDriver
     */
    public function setPath(string $path): self
    {
        $this->path = $path;
        return $this;
    }


    /**
     * General save.
     *
     * @param array  $values
     * @param string $dataIndex
     * @return int
     * @throws UniqueConstraintViolationException
     */
    private function generalSave(array $values, $dataIndex): int
    {
        $id = $values['id'];
        unset($values['id']);

        if (!$id) {
            // add
            if (!in_array($values[$dataIndex], $this->data[$dataIndex])) {
                $this->data[$dataIndex][] = $values[$dataIndex];
            } else {
                throw new UniqueConstraintViolationException('Item already exist!');
            }
        } else {
            // update
            if ($values) {
                if ($id != $values[$dataIndex]) {
                    $index = array_search($id, $this->data[$dataIndex]);
                    if ($index !== false && !in_array($values[$dataIndex], $this->data[$dataIndex])) {
                        $this->data[$dataIndex][$index] = $values[$dataIndex];
                    } else {
                        throw new UniqueConstraintViolationException('Item already exist!');
                    }
                } else {
                    return 0;
                }
            } else {
                // delete
                $index = array_search($id, $this->data[$dataIndex]);
                if ($index !== false) {
                    unset($this->data[$dataIndex][$index]);
                    $this->data[$dataIndex] = array_values($this->data[$dataIndex]);    // correct fix for index array
                }
            }
        }
        return file_put_contents($this->path, Neon::encode($this->data, Neon::BLOCK));
    }


    /**
     * Save role.
     *
     * @param array $values
     * @return int
     * @throws UniqueConstraintViolationException
     */
    public function saveRole(array $values): int
    {
        return $this->generalSave($values, 'role');
    }


    /**
     * Save resource.
     *
     * @param array $values
     * @return int
     * @throws UniqueConstraintViolationException
     */
    public function saveResource(array $values): int
    {
        return $this->generalSave($values, 'resource');
    }


    /**
     * Save privilege.
     *
     * @param array $values
     * @return int
     * @throws UniqueConstraintViolationException
     */
    public function savePrivilege(array $values): int
    {
        return $this->generalSave($values, 'privilege');
    }


    /**
     * Save acl.
     *
     * @param string $idRole
     * @param array  $values
     * @param bool   $deleteBeforeSave
     * @return int
     */
    public function saveAcl(string $idRole, array $values, bool $deleteBeforeSave = true): int
    {
        if ($deleteBeforeSave) {
            // delete all acl for idRole
            unset($this->data['acl'][$idRole]);
        }

        // save all to role
        if (isset($values['all']) && $values['all']) {
            $this->data['acl'][$idRole] = 'all';
            return file_put_contents($this->path, Neon::encode($this->data, Neon::BLOCK));
        }

        // save acl by role && resource
        foreach ($values as $idResource => $item) {
            if ($item && is_array($item)) {
                if (!in_array('all', $item)) {
                    if (!isset($this->data['acl'][$idRole][$idResource])) {
                        $this->data['acl'][$idRole][$idResource] = $item;
                    } else {
                        $this->data['acl'][$idRole][$idResource] = array_merge($this->data['acl'][$idRole][$idResource], $item);
                    }
                } else {
                    $this->data['acl'][$idRole][$idResource][] = 'all';
                }
            }
        }
        return file_put_contents($this->path, Neon::encode($this->data, Neon::BLOCK));
    }
}
