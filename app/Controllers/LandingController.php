<?php

namespace App\Controllers;

class LandingController extends BaseController
{
    private const LANDING_PAGES = [
        '404',
        'about',
        'become-a-teacher',
        'blog',
        'blog-carousel',
        'blog-details',
        'blog-list',
        'cart',
        'checkout',
        'coming-soon',
        'contact',
        'course',
        'course-carousel',
        'course-details',
        'course-list',
        'event-details',
        'events',
        'events-carousel',
        'faq',
        'gallery',
        'index-dark',
        'instructor',
        'instructor-carousel',
        'instructor-details',
        'login',
        'pricing',
        'product-details',
        'products',
        'sign-up',
        'testimonials',
        'testimonials-carousel',
        'wishlist',
    ];

    public function index()
    {
        return view('landing/index');
    }

    public function landingPage(string $page)
    {
        if (! in_array($page, self::LANDING_PAGES, true)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('landing/pages/' . $page, [
            'title' => ucwords(str_replace('-', ' ', $page)) . ' | Begawipbj',
        ]);
    }
}
