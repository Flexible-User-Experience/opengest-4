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
        return [
            ['/']
            ['/servicio/my-title']
            ['/empresa']
            ['/vehiculo/my-vehicle-category/my-title']
            ['/vehiculos/categoria/grues2']
            ['/trabajos']
            ['/trabajo/my-title']
            ['/accesorios']
            ['/accesorio/my-title']
            ['/sobre-este-sitio']
            ['/privacidad']
            ['/mapa-del-web']
            ['/sitemap/sitemap.xml']
            ['/sitemap/sitemap.default.xml']
        ];
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
        return [
            ['/ca/pagina-trenacada']
            ['/es/pagina-rota']
            ['/en/broken-page']
        ];
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

    /**
     * Urls provider.
     *
     * @return array
     */
    public function provideRedirectedUrls()
    {
        return [
            ['/servicios']
            ['/vehiculos']
        ];
    }
}
