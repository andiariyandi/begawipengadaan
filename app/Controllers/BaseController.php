<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = ['url', 'text', 'form'];

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);
    }

    /**
     * Validates that state-mutating requests (delete/toggle) either come via POST
     * or via GET with a trusted internal same-origin Referer.
     */
    protected function validateSafeMutation(): ?ResponseInterface
    {
        $req = $this->request ?? service('request');
        if (strtoupper($req->getMethod()) === 'GET') {
            $referer = (string) $req->getServer('HTTP_REFERER');
            $siteOrigin = rtrim(site_url(), '/');
            if ($referer === '' || ! str_starts_with($referer, $siteOrigin)) {
                return service('response')->setStatusCode(403)->setBody('Akses ditolak: Operasi mutasi data memerlukan metode POST yang aman atau Referer internal yang valid.');
            }
        }

        return null;
    }
}
