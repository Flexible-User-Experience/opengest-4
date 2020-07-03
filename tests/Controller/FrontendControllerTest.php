<?php

namespace App\Tests\Controller;

use App\Tests\AbstractBaseTest;

/**
 * Class FrontendControllerTest.
 *
 * @category Test
 */
class FrontendControllerTest extends AbstractBaseTest
{
    /**
     * Urls provider.
     *
     * @return array
     */
    public function provideRedirectedUrls()
    {
        return array(
            array('/servicios'),
            array('/vehiculos'),
        );
    }

    /**
     * Test HTTP request is successful.
     *
     * @dataProvider provideSuccessfulUrls
     *
     * @param string $url
     */
    public function testPagesAreSuccessful($url)
    {
        $client = $this->getAnonymousUserClient();
        $client->request('GET', $url);

        $this->assertResponseIsSuccessful();
    }

    /**
     * Successful Urls provider.
     *
     * @return array
     */
    public function provideSuccessfulUrls()
    {
        return array(
            array('/'),
            array('/servicio/my-title'),
            array('/empresa'),
            array('/vehiculo/my-vehicle-category/my-title'),
            array('/vehiculos/categoria/grues2'),
            array('/trabajos'),
            array('/trabajo/my-title'),
            array('/accesorios'),
            array('/accesorio/my-title'),
            array('/sobre-este-sitio'),
            array('/privacidad'),
            array('/mapa-del-web'),
            array('/sitemap/sitemap.xml'),
            array('/sitemap/sitemap.default.xml'),
        );
    }

    /**
     * Test HTTP request is not found.
     *
     * @dataProvider provideNotFoundUrls
     *
     * @param string $url
     */
    public function testPagesAreNotFound($url)
    {
        $client = $this->getAnonymousUserClient();
        $client->request('GET', $url);

        $this->assertResponseStatusCodeSame(404);
    }

    /**
     * Not found Urls provider.
     *
     * @return array
     */
    public function provideNotFoundUrls()
    {
        return array(
            array('/ca/pagina-trenacada'),
            array('/es/pagina-rota'),
            array('/en/broken-page'),
        );
    }

    /**
     * Test HTTP request is redirected.
     *
     * @dataProvider provideRedirectedUrls
     *
     * @param string $url
     */
    public function testFrontendPagesAreRedirected($url)
    {
        $client = $this->getAnonymousUserClient();
        $client->request('GET', $url);

        $this->assertResponseStatusCodeSame(302);
    }
}
