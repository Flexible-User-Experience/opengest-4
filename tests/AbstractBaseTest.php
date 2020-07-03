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
    protected function getAnonymousUserClient()
    {
        return WebTestCase::createClient();
    }

    /**
     * @return KernelBrowser
     */
    protected function getAuthenticatedUserClient()
    {
        return WebTestCase::createClient();
    }
}
