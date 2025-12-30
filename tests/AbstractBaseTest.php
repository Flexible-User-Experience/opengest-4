<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class abstract base test.
 *
 * @category Test
 */
abstract class AbstractBaseTest extends WebTestCase
{
    /**
     * @return KernelBrowser
     */
    protected function getAnonymousUserClient(): KernelBrowser
    {
        return WebTestCase::createClient();
    }

    /**
     * @return KernelBrowser
     */
    protected function getAuthenticatedUserClient(): KernelBrowser
    {
        return WebTestCase::createClient([], [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'passwd',
        ]);
    }
}
