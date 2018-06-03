<?php declare(strict_types=1);

namespace Identity\Authorizator\Bridges\Tracy;

use Exception;
use Identity\Authorizator\IIdentityAuthorizator;
use Latte\Engine;
use Nette\Application\Application;
use Nette\DI\Container;
use Nette\SmartObject;
use Tracy\IBarPanel;


/**
 * Class Panel
 *
 * @author  geniv
 * @package Authorizator\Bridges\Tracy
 */
class Panel implements IBarPanel
{
    use SmartObject;

    /** @var IIdentityAuthorizator */
    private $identityAuthorizator;
    /** @var Container */
    private $container;


    /**
     * Panel constructor.
     *
     * @param IIdentityAuthorizator $identityAuthorizator
     * @param Container             $container
     */
    public function __construct(IIdentityAuthorizator $identityAuthorizator, Container $container)
    {
        $this->identityAuthorizator = $identityAuthorizator;

        $this->container = $container;
    }


    /**
     * Renders HTML code for custom tab.
     *
     * @return string
     */
    public function getTab(): string
    {
        return '<span title="Authorizator">' .
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512.001 512.001" width="16" height="16">' .
            '<path d="M478.511 88.622C403.694 69.223 328.437 39.017 260.878 1.269a10.005 10.005 0 0 0-9.755 0c-69.502 38.83-140.691 67.403-217.634 87.354a10 10 0 0 0-7.49 9.68v110.394c0 113.535 55.292 188.639 101.678 231.65 49.757 46.138 107.46 71.655 128.324 71.655 20.864 0 78.567-25.517 128.324-71.655 46.384-43.011 101.678-118.114 101.678-231.65V98.302a10.003 10.003 0 0 0-7.492-9.68zM370.726 425.678c-27.557 25.553-54.336 41.685-71.95 50.718-22.319 11.447-37.81 15.603-42.776 15.603-4.966 0-20.456-4.155-42.776-15.603-17.612-9.033-44.392-25.165-71.95-50.718-43.464-40.303-95.276-110.663-95.276-216.983V106.008c73.964-19.8 142.801-47.523 210.003-84.574 65.498 36.059 137.827 65.185 210 84.571v102.69h.001c-.001 106.32-51.812 176.68-95.276 216.983z"/><g style="transform: scale(1.4) translateY(15px); transform-origin: center center;">' .
            '<path d="M308.898 209.077v-32.921c0-29.168-23.73-52.898-52.9-52.898-29.168 0-52.898 23.73-52.898 52.898v32.921c-11.898.429-21.447 10.235-21.447 22.236v76.769c-.001 12.273 9.984 22.257 22.257 22.257h104.18c12.273 0 22.258-9.984 22.258-22.257v-76.769c0-12.002-9.55-21.809-21.45-22.236zm-85.799-32.921c0-18.14 14.759-32.898 32.9-32.898 18.14 0 32.898 14.758 32.898 32.898v32.9h-65.799v-32.9zm87.248 131.927c0 1.224-1.034 2.257-2.258 2.257H203.908c-1.224 0-2.258-1.033-2.258-2.257v-76.769h.001c0-1.224 1.034-2.257 2.258-2.257h104.18c1.224 0 2.258 1.033 2.258 2.257v76.769z"/>' .
            '<path d="M256 249.64c-7.701 0-13.982 6.281-13.982 13.982 0 3.786 1.523 7.225 3.982 9.746v9.787c0 5.522 4.477 10 10 10s10-4.478 10-10v-9.787c2.459-2.522 3.982-5.96 3.982-9.746 0-7.701-6.281-13.982-13.982-13.982z"/></g></svg>' .
            'Authorizator' .
            '</span>';
    }


    /**
     * Renders HTML code for custom panel.
     *
     * @return string
     */
    public function getPanel(): string
    {
        $application = $this->container->getByType(Application::class);    // load system application
        $presenter = $application->getPresenter();

        // callback
        $isAllowed = function ($item) {
            return $this->identityAuthorizator->isAllowed($item['role'], $item['resource'], $item['privilege']);
        };

        // callback
        $acl = $this->identityAuthorizator->getAcl();
        $isDefine = function ($item) use ($acl) {
            $callback = function ($row) use ($item) {
                if ($item['role'] && $item['resource'] && $item['privilege']) {
                    return $row['role'] == $item['role'] && $row['resource'] == $item['resource'] &&
                        ($row['privilege'] == $item['privilege'] || $row['privilege'] == 'all');
                }
                if ($item['role'] && $item['resource']) {
                    return $row['role'] == $item['role'] && $row['resource'] == $item['resource'];
                }
                if ($item['role']) {
                    return $row['role'] == $item['role'];
                }
                return true;
            };
            $result = array_values(array_filter($acl, $callback));
            return ($result);
        };

        // callback
        $addAcl = function ($item) use ($presenter) {
            try {
                return $presenter->link('AddAcl!', $item['role'], $item['resource'], $item['privilege']);
            } catch (Exception $e) {
            }
        };

        $policy = $this->identityAuthorizator->getPolicy();
        $params = [
            'class'             => get_class($this->identityAuthorizator),
            'policy'            => $policy,           // policy
            'policyDescription' => IIdentityAuthorizator::POLICY_DESCRIPTION[$policy],  // policy
            'listCurrentAcl'    => $this->identityAuthorizator->getListCurrentAcl(),    // list current acl
            'isAllowed'         => $isAllowed,                                          // callback isAllowed
            'isDefine'          => $isDefine,                                           // callback isDefine
            'addAcl'            => $addAcl,                                             // callback addAcl
        ];
        $latte = new Engine;
        return $latte->renderToString(__DIR__ . '/PanelTemplate.latte', $params);
    }
}
