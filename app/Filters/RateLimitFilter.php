<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RateLimitFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $key = 'route_' . hash('sha256', $request->getUri()->getPath() . '|' . $request->getIPAddress());

        if (! service('throttler')->check($key, 10, 60)) {
            return service('response')
                ->setStatusCode(429)
                ->setBody('Terlalu banyak permintaan. Silakan coba kembali nanti.');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
