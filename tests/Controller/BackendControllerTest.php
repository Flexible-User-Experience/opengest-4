<?php

namespace App\Tests\Controller;

use App\Tests\AbstractBaseTest;

/**
 * Class BackendControllerTest.
 *
 * @category Test
 */
class BackendControllerTest extends AbstractBaseTest
{
    /**
     * Test admin login request is successful.
     */
    public function testAdminLoginPageIsSuccessful()
    {
        $client = $this->getAnonymousUserClient();
        $client->request('GET', '/admin/login');

        $this->assertResponseIsSuccessful();
    }

    /**
     * Test HTTP request is successful.
     *
     * @dataProvider provideSuccessfulUrls
     *
     * @param string $url
     */
    public function testAdminPagesAreSuccessful($url)
    {
        $client = $this->getAuthenticatedUserClient();
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
            // Web
            array('/admin/dashboard'),
            array('/admin/web/servei/list'),
            array('/admin/web/servei/create'),
            array('/admin/web/servei/1/edit'),
            array('/admin/web/treball/list'),
            array('/admin/web/treball/create'),
            array('/admin/web/treball/1/edit'),
            array('/admin/web/imatge-treball/list'),
            array('/admin/web/imatge-treball/create'),
            array('/admin/web/imatge-treball/1/edit'),
            array('/admin/web/imatge-treball/1/delete'),
            array('/admin/web/accesori/list'),
            array('/admin/web/accesori/create'),
            array('/admin/web/accesori/1/edit'),
            array('/admin/web/missatge-contacte/list'),
            array('/admin/web/missatge-contacte/1/show'),
            array('/admin/web/missatge-contacte/1/answer'),
            // Operator
            array('/admin/operaris/operari/list'),
            array('/admin/operaris/operari/create'),
            array('/admin/operaris/operari/1/edit'),
            array('/admin/operaris/tipus-revisio/list'),
            array('/admin/operaris/tipus-revisio/create'),
            array('/admin/operaris/tipus-revisio/1/edit'),
            array('/admin/operaris/revisio/list'),
            array('/admin/operaris/revisio/create'),
            array('/admin/operaris/revisio/1/edit'),
            array('/admin/operaris/tipus-absencia/list'),
            array('/admin/operaris/tipus-absencia/create'),
            array('/admin/operaris/tipus-absencia/1/edit'),
            array('/admin/operaris/absencia/list'),
            array('/admin/operaris/absencia/create'),
            array('/admin/operaris/absencia/1/edit'),
            array('/admin/operaris/tacograf/list'),
            array('/admin/operaris/tacograf/create'),
            array('/admin/operaris/tacograf/1/edit'),
            array('/admin/operaris/imports-varis/list'),
            array('/admin/operaris/imports-varis/create'),
            array('/admin/operaris/imports-varis/1/edit'),
            // Vehicle
            array('/admin/vehicles/vehicle/list'),
            array('/admin/vehicles/vehicle/create'),
            array('/admin/vehicles/vehicle/1/edit'),
            array('/admin/vehicles/categoria-vehicle/list'),
            array('/admin/vehicles/categoria-vehicle/create'),
            array('/admin/vehicles/categoria-vehicle/1/edit'),
            array('/admin/vehicles/revisio/list'),
            array('/admin/vehicles/revisio/create'),
            array('/admin/vehicles/revisio/1/edit'),
            array('/admin/vehicles/tipus-revisio/list'),
            array('/admin/vehicles/tipus-revisio/create'),
            array('/admin/vehicles/tipus-revisio/1/edit'),
            array('/admin/vehicles/tacograf/list'),
            array('/admin/vehicles/tacograf/create'),
            array('/admin/vehicles/tacograf/1/edit'),
            // Partner
            array('/admin/tercers/classe/list'),
            array('/admin/tercers/classe/create'),
            array('/admin/tercers/classe/1/edit'),
            array('/admin/tercers/classe/1/delete'),
            array('/admin/tercers/tipus/list'),
            array('/admin/tercers/tipus/create'),
            array('/admin/tercers/tipus/1/edit'),
            array('/admin/tercers/tipus/1/delete'),
            array('/admin/tercers/tercer/list'),
            array('/admin/tercers/tercer/create'),
            array('/admin/tercers/tercer/1/edit'),
            array('/admin/tercers/tercer/1/delete'),
            array('/admin/tercers/comandes/list'),
            array('/admin/tercers/comandes/create'),
            array('/admin/tercers/comandes/1/edit'),
            array('/admin/tercers/comandes/1/delete'),
            array('/admin/tercers/obres/list'),
            array('/admin/tercers/obres/create'),
            array('/admin/tercers/obres/1/edit'),
            array('/admin/tercers/obres/1/delete'),
            array('/admin/tercers/contacte/list'),
            array('/admin/tercers/contacte/create'),
            array('/admin/tercers/contacte/1/edit'),
            array('/admin/tercers/contacte/1/delete'),
            array('/admin/tercers/dies-inhabils/list'),
            array('/admin/tercers/dies-inhabils/create'),
            array('/admin/tercers/dies-inhabils/1/edit'),
            array('/admin/tercers/dies-inhabils/1/delete'),
            // Enterprise
            array('/admin/empreses/empresa/list'),
            array('/admin/empreses/empresa/create'),
            array('/admin/empreses/empresa/1/edit'),
            array('/admin/empreses/grup-prima/list'),
            array('/admin/empreses/grup-prima/create'),
            array('/admin/empreses/grup-prima/1/edit'),
            array('/admin/empreses/grup-prima/1/delete'),
            array('/admin/empreses/compte-bancari/list'),
            array('/admin/empreses/compte-bancari/create'),
            array('/admin/empreses/compte-bancari/1/edit'),
            array('/admin/empreses/compte-bancari/1/delete'),
            array('/admin/empreses/dies-festius/list'),
            array('/admin/empreses/dies-festius/create'),
            array('/admin/empreses/dies-festius/1/edit'),
            array('/admin/empreses/dies-festius/1/delete'),
            array('/admin/empreses/linies-activitat/list'),
            array('/admin/empreses/linies-activitat/create'),
            array('/admin/empreses/linies-activitat/1/edit'),
            array('/admin/empreses/linies-activitat/1/delete'),
            array('/admin/empreses/tipus-document-cobrament/list'),
            array('/admin/empreses/tipus-document-cobrament/create'),
            array('/admin/empreses/tipus-document-cobrament/1/edit'),
            array('/admin/empreses/tipus-document-cobrament/1/delete'),
            // Sale
            array('/admin/vendes/tarifa/list'),
            array('/admin/vendes/tarifa/create'),
            array('/admin/vendes/tarifa/1/edit'),
            array('/admin/vendes/peticio/list'),
            array('/admin/vendes/peticio/create'),
            array('/admin/vendes/peticio/1/edit'),
            array('/admin/vendes/peticio/1/delete'),
            array('/admin/vendes/albara/list'),
            array('/admin/vendes/albara/create'),
            array('/admin/vendes/albara/1/edit'),
            array('/admin/vendes/albara/1/delete'),
            array('/admin/vendes/albara-linia/create'),
            array('/admin/vendes/albara-linia/1/edit'),
            array('/admin/vendes/albara-linia/1/delete'),
            array('/admin/vendes/factura/list'),
            array('/admin/vendes/factura/create'),
            array('/admin/vendes/factura/1/edit'),
            array('/admin/vendes/valoracio-peticio-albara/list'),
            array('/admin/vendes/valoracio-peticio-albara/create'),
            array('/admin/vendes/valoracio-peticio-albara/1/edit'),
            array('/admin/vendes/valoracio-peticio-albara/1/delete'),
            // Setting
            array('/admin/configuracio/provincia/list'),
            array('/admin/configuracio/provincia/create'),
            array('/admin/configuracio/provincia/1/edit'),
            array('/admin/configuracio/ciutat/list'),
            array('/admin/configuracio/ciutat/create'),
            array('/admin/configuracio/ciutat/1/edit'),
            array('/admin/configuracio/usuari/list'),
            array('/admin/configuracio/usuari/create'),
            array('/admin/configuracio/usuari/1/edit'),
            array('/admin/configuracio/usuari/1/delete'),
            array('/admin/configuracio/series-factura/list'),
            array('/admin/configuracio/series-factura/create'),
            array('/admin/configuracio/series-factura/1/edit'),
            array('/admin/configuracio/series-factura/1/delete'),
        );
    }

    /**
     * Test HTTP request is not found.
     *
     * @dataProvider provideNotFoundUrls
     *
     * @param string $url
     */
    public function testAdminPagesAreNotFound($url)
    {
        $client = $this->getAuthenticatedUserClient();
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
            // Web
            array('/admin/web/servei/1/show'),
            array('/admin/web/servei/batch'),
            array('/admin/web/servei/1/delete'),
            array('/admin/web/treball/1/show'),
            array('/admin/web/treball/batch'),
            array('/admin/web/treball/1/delete'),
            array('/admin/web/imatge-treball/1/show'),
            array('/admin/web/imatge-treball/batch'),
            array('/admin/web/accesori/1/show'),
            array('/admin/web/accesori/batch'),
            array('/admin/web/accesori/1/delete'),
            array('/admin/web/missatge-contacte/create'),
            array('/admin/web/missatge-contacte/1/edit'),
            array('/admin/web/missatge-contacte/1/delete'),
            // Operator
            array('/admin/operaris/operari/1/delete'),
            array('/admin/operaris/operari/1/show'),
            array('/admin/operaris/operari/batch'),
            array('/admin/operaris/tipus-revisio/1/delete'),
            array('/admin/operaris/tipus-revisio/1/show'),
            array('/admin/operaris/tipus-revisio/batch'),
            array('/admin/operaris/revisio/1/delete'),
            array('/admin/operaris/revisio/1/show'),
            array('/admin/operaris/revisio/batch'),
            array('/admin/operaris/tipus-absencia/1/delete'),
            array('/admin/operaris/tipus-absencia/1/show'),
            array('/admin/operaris/tipus-absencia/batch'),
            array('/admin/operaris/absencia/1/delete'),
            array('/admin/operaris/absencia/1/show'),
            array('/admin/operaris/absencia/batch'),
            array('/admin/operaris/tacograf/1/delete'),
            array('/admin/operaris/tacograf/1/show'),
            array('/admin/operaris/tacograf/batch'),
            array('/admin/operaris/imports-varis/1/show'),
            array('/admin/operaris/imports-varis/batch'),
            // Vehicle
            array('/admin/vehicles/vehicle/1/show'),
            array('/admin/vehicles/vehicle/batch'),
            array('/admin/vehicles/vehicle/1/delete'),
            array('/admin/vehicles/categoria-vehicle/1/show'),
            array('/admin/vehicles/categoria-vehicle/batch'),
            array('/admin/vehicles/categoria-vehicle/1/delete'),
            array('/admin/vehicles/revisio/1/delete'),
            array('/admin/vehicles/revisio/1/show'),
            array('/admin/vehicles/revisio/batch'),
            array('/admin/vehicles/tipus-revisio/1/delete'),
            array('/admin/vehicles/tipus-revisio/1/show'),
            array('/admin/vehicles/tipus-revisio/batch'),
            array('/admin/vehicles/tacograf/delete'),
            array('/admin/vehicles/tacograf/show'),
            array('/admin/vehicles/tacograf/1/batch'),
            // Partner
            array('/admin/tercers/classe/1/show'),
            array('/admin/tercers/classe/batch'),
            array('/admin/tercers/tipus/1/show'),
            array('/admin/tercers/tipus/batch'),
            array('/admin/tercers/tercer/1/show'),
            array('/admin/tercers/tercer/batch'),
            array('/admin/tercers/comandes/1/show'),
            array('/admin/tercers/comandes/batch'),
            array('/admin/tercers/obres/1/show'),
            array('/admin/tercers/obres/batch'),
            array('/admin/tercers/contacte/1/show'),
            array('/admin/tercers/contacte/batch'),
            array('/admin/tercers/dies-inhabils/1/show'),
            array('/admin/tercers/dies-inhabils/batch'),
            // Enterprise
            array('/admin/empreses/empresa/1/show'),
            array('/admin/empreses/empresa/batch'),
            array('/admin/empreses/empresa/1/delete'),
            array('/admin/empreses/grup-prima/1/show'),
            array('/admin/empreses/grup-prima/batch'),
            array('/admin/empreses/compte-bancari/1/show'),
            array('/admin/empreses/compte-bancari/batch'),
            array('/admin/empreses/dies-festius/1/show'),
            array('/admin/empreses/dies-festius/batch'),
            array('/admin/empreses/linies-activitat/1/show'),
            array('/admin/empreses/linies-activitat/batch'),
            array('/admin/empreses/tipus-document-cobrament/1/show'),
            array('/admin/empreses/tipus-document-cobrament/batch'),
            // Sale
            array('/admin/vendes/tarifa/1/show'),
            array('/admin/vendes/tarifa/batch'),
            array('/admin/vendes/tarifa/1/delete'),
            array('/admin/vendes/peticio/1/show'),
            array('/admin/vendes/albara/1/show'),
            array('/admin/vendes/albara/batch'),
            array('/admin/vendes/albara-linia/1/show'),
            array('/admin/vendes/albara-linia/batch'),
            array('/admin/vendes/factura/1/show'),
            array('/admin/vendes/factura/1/delete'),
            array('/admin/vendes/factura/batch'),
            array('/admin/vendes/valoracio-peticio-albara/1/show'),
            array('/admin/vendes/valoracio-peticio-albara/batch'),
            // Setting
            array('/admin/configuracio/provincia/1/show'),
            array('/admin/configuracio/provincia/batch'),
            array('/admin/configuracio/provincia/1/delete'),
            array('/admin/configuracio/ciutat/1/show'),
            array('/admin/configuracio/ciutat/batch'),
            array('/admin/configuracio/ciutat/1/delete'),
            array('/admin/configuracio/usuari/1/show'),
            array('/admin/configuracio/usuari/batch'),
            array('/admin/configuracio/series-factura/1/show'),
            array('/admin/configuracio/series-factura/batch'),
        );
    }
}
