<?php

declare(strict_types=1);

namespace Infrangible\CacheUsage\Plugin\Framework\App;

use Infrangible\CacheUsage\Model\Cache;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Router\Base;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class RouterInterface
{
    /** @var Cache */
    protected $cache;

    /** @var Http */
    protected $request;

    public function __construct(Cache $cache, Http $request)
    {
        $this->cache = $cache;
        $this->request = $request;
    }

    public function afterMatch(\Magento\Framework\App\RouterInterface $subject, $matched)
    {
        if ($matched && $subject instanceof Base) {
            $this->cache->setRouteName($this->request->getRouteName());
            $this->cache->setControllerName($this->request->getControllerName());
            $this->cache->setActionName($this->request->getActionName());
            $this->cache->setPathParameters(http_build_query($this->request->getUserParams()));
            $this->cache->setQueryParameters(http_build_query($this->request->getQuery()));
        }

        return $matched;
    }
}
