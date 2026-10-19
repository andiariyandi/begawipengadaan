<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

// Enforce Asia/Jakarta (WIB / GMT+7) across the entire application runtime
if (date_default_timezone_get() !== 'Asia/Jakarta') {
    date_default_timezone_set('Asia/Jakarta');
}

if (! function_exists('csrf_token')) {
    function csrf_token(): string
    {
        if (is_cli() && ! (service('request') instanceof \CodeIgniter\HTTP\IncomingRequest)) {
            return config('Security')->tokenName ?? 'csrf_test_name';
        }
        return service('security')->getTokenName();
    }
}

if (! function_exists('csrf_hash')) {
    function csrf_hash(): string
    {
        if (is_cli() && ! (service('request') instanceof \CodeIgniter\HTTP\IncomingRequest)) {
            return 'cli_dummy_csrf_hash';
        }
        return service('security')->getHash();
    }
}

if (! function_exists('csrf_field')) {
    function csrf_field(?string $id = null): string
    {
        return '<input type="hidden"' . ($id !== null ? ' id="' . esc($id, 'attr') . '"' : '') . ' name="' . csrf_token() . '" value="' . csrf_hash() . '" />';
    }
}

if (! function_exists('setting')) {
    /**
     * Retrieve a site setting by key, e.g. setting('organization.agency_name') or setting('agency_name')
     */
    function setting(string $key, mixed $default = null): mixed
    {
        static $service = null;
        if ($service === null) {
            $service = new \App\Services\SystemSettingService();
        }
        return $service->getSetting($key, $default);
    }
}

