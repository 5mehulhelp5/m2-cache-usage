<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CacheUsage\Plugin\Framework\App;

use Infrangible\CacheUsage\Model\Cache;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Router\Base;

/**
 * Records the route, controller, action and parameters of the request, when the standard router matches it (also
 * after the URL rewrite router forwarded it).
 */
class RouterInterface
{
    /**
     * @param Cache $cache
     * @param Http $request
     */
    public function __construct(protected Cache $cache, protected Http $request)
    {
    }

    /**
     * Records the matched route of the request.
     *
     * @param \Magento\Framework\App\RouterInterface $subject
     * @param mixed $matched the action, or null or false if the router does not match
     *
     * @return mixed
     */
    public function afterMatch(\Magento\Framework\App\RouterInterface $subject, mixed $matched): mixed
    {
        if ($matched && $subject instanceof Base) {
            $query = $this->request->getQueryValue();

            $this->cache->setRouteName($this->request->getRouteName());
            $this->cache->setControllerName($this->request->getControllerName());
            $this->cache->setActionName($this->request->getActionName());
            $this->cache->setPathParameters(http_build_query($this->request->getUserParams()));
            $this->cache->setQueryParameters(http_build_query(is_array($query) ? $query : []));
        }

        return $matched;
    }
}
