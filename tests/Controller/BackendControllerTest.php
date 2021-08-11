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
        return [
            // Web
            ['/admin/dashboard'],
            ['/admin/web/servei/list'],
            ['/admin/web/servei/create'],
            ['/admin/web/servei/1/edit'],
            ['/admin/web/treball/list'],
            ['/admin/web/treball/create'],
            ['/admin/web/treball/1/edit'],
            ['/admin/web/imatge-treball/list'],
            ['/admin/web/imatge-treball/create'],
            ['/admin/web/imatge-treball/1/edit'],
            ['/admin/web/imatge-treball/1/delete'],
            ['/admin/web/accesori/list'],
            ['/admin/web/accesori/create'],
            ['/admin/web/accesori/1/edit'],
            ['/admin/web/missatge-contacte/list'],
            ['/admin/web/missatge-contacte/1/show'],
            ['/admin/web/missatge-contacte/1/answer'],
            // Operator
            ['/admin/operaris/operari/list'],
            ['/admin/operaris/operari/create'],
            ['/admin/operaris/operari/1/edit'],
            ['/admin/operaris/tipus-revisio/list'],
            ['/admin/operaris/tipus-revisio/create'],
            ['/admin/operaris/tipus-revisio/1/edit'],
            ['/admin/operaris/revisio/list'],
            ['/admin/operaris/revisio/create'],
            ['/admin/operaris/revisio/1/edit'],
            ['/admin/operaris/tipus-absencia/list'],
            ['/admin/operaris/tipus-absencia/create'],
            ['/admin/operaris/tipus-absencia/1/edit'],
            ['/admin/operaris/absencia/list'],
            ['/admin/operaris/absencia/create'],
            ['/admin/operaris/absencia/1/edit'],
            ['/admin/operaris/tacograf/list'],
            ['/admin/operaris/tacograf/create'],
            ['/admin/operaris/tacograf/1/edit'],
            ['/admin/operaris/imports-varis/list'],
            ['/admin/operaris/imports-varis/create'],
            ['/admin/operaris/imports-varis/1/edit'],
            ['/admin/operaris/partes-trabajo/list'],
            ['/admin/operaris/partes-trabajo/create'],
            ['/admin/operaris/partes-trabajo/1/edit'],
            // Vehicle
            ['/admin/vehicles/vehicle/list'],
            ['/admin/vehicles/vehicle/create'],
            ['/admin/vehicles/vehicle/1/edit'],
            ['/admin/vehicles/categoria-vehicle/list'],
            ['/admin/vehicles/categoria-vehicle/create'],
            ['/admin/vehicles/categoria-vehicle/1/edit'],
            ['/admin/vehicles/revisio/list'],
            ['/admin/vehicles/revisio/create'],
            ['/admin/vehicles/revisio/1/edit'],
            ['/admin/vehicles/tipus-revisio/list'],
            ['/admin/vehicles/tipus-revisio/create'],
            ['/admin/vehicles/tipus-revisio/1/edit'],
            ['/admin/vehicles/tacograf/list'],
            ['/admin/vehicles/tacograf/create'],
            ['/admin/vehicles/tacograf/1/edit'],
            // Partner
            ['/admin/tercers/classe/list'],
            ['/admin/tercers/classe/create'],
            ['/admin/tercers/classe/1/edit'],
            ['/admin/tercers/classe/1/delete'],
            ['/admin/tercers/tipus/list'],
            ['/admin/tercers/tipus/create'],
            ['/admin/tercers/tipus/1/edit'],
            ['/admin/tercers/tipus/1/delete'],
            ['/admin/tercers/tercer/list'],
            ['/admin/tercers/tercer/create'],
            ['/admin/tercers/tercer/1/edit'],
            ['/admin/tercers/tercer/1/delete'],
            ['/admin/tercers/comandes/list'],
            ['/admin/tercers/comandes/create'],
            ['/admin/tercers/comandes/1/edit'],
            ['/admin/tercers/comandes/1/delete'],
            ['/admin/tercers/obres/list'],
            ['/admin/tercers/obres/create'],
            ['/admin/tercers/obres/1/edit'],
            ['/admin/tercers/obres/1/delete'],
            ['/admin/tercers/contacte/list'],
            ['/admin/tercers/contacte/create'],
            ['/admin/tercers/contacte/1/edit'],
            ['/admin/tercers/contacte/1/delete'],
            ['/admin/tercers/dies-inhabils/list'],
            ['/admin/tercers/dies-inhabils/create'],
            ['/admin/tercers/dies-inhabils/1/edit'],
            ['/admin/tercers/dies-inhabils/1/delete'],
            // Enterprise
            ['/admin/empreses/empresa/list'],
            ['/admin/empreses/empresa/create'],
            ['/admin/empreses/empresa/1/edit'],
            ['/admin/empreses/grup-prima/list'],
            ['/admin/empreses/grup-prima/create'],
            ['/admin/empreses/grup-prima/1/edit'],
            ['/admin/empreses/grup-prima/1/delete'],
            ['/admin/empreses/compte-bancari/list'],
            ['/admin/empreses/compte-bancari/create'],
            ['/admin/empreses/compte-bancari/1/edit'],
            ['/admin/empreses/compte-bancari/1/delete'],
            ['/admin/empreses/dies-festius/list'],
            ['/admin/empreses/dies-festius/create'],
            ['/admin/empreses/dies-festius/1/edit'],
            ['/admin/empreses/dies-festius/1/delete'],
            ['/admin/empreses/linies-activitat/list'],
            ['/admin/empreses/linies-activitat/create'],
            ['/admin/empreses/linies-activitat/1/edit'],
            ['/admin/empreses/linies-activitat/1/delete'],
            ['/admin/empreses/tipus-document-cobrament/list'],
            ['/admin/empreses/tipus-document-cobrament/create'],
            ['/admin/empreses/tipus-document-cobrament/1/edit'],
            ['/admin/empreses/tipus-document-cobrament/1/delete'],
            // Sale
            ['/admin/vendes/tarifa/list'],
            ['/admin/vendes/tarifa/create'],
            ['/admin/vendes/tarifa/1/edit'],
            ['/admin/vendes/peticio/list'],
            ['/admin/vendes/peticio/create'],
            ['/admin/vendes/peticio/1/edit'],
            ['/admin/vendes/peticio/1/delete'],
            ['/admin/vendes/albara/list'],
            ['/admin/vendes/albara/create'],
            ['/admin/vendes/albara/1/edit'],
            ['/admin/vendes/albara/1/delete'],
            ['/admin/vendes/albara-linia/create'],
            ['/admin/vendes/albara-linia/1/edit'],
            ['/admin/vendes/albara-linia/1/delete'],
            ['/admin/vendes/factura/list'],
            ['/admin/vendes/factura/create'],
            ['/admin/vendes/factura/1/edit'],
            ['/admin/vendes/valoracio-peticio-albara/list'],
            ['/admin/vendes/valoracio-peticio-albara/create'],
            ['/admin/vendes/valoracio-peticio-albara/1/edit'],
            ['/admin/vendes/valoracio-peticio-albara/1/delete'],
            ['/admin/vendes/items/list'],
            ['/admin/vendes/items/create'],
            ['/admin/vendes/items/1/edit'],
            ['/admin/vendes/serveis_tarifa/list'],
            ['/admin/vendes/serveis_tarifa/create'],
            ['/admin/vendes/serveis_tarifa/1/edit'],
//            ['/admin/vendes/serveis_tarifa/1/get-json-sale-tariff-by-id '],
            // Setting
            ['/admin/configuracio/provincia/list'],
            ['/admin/configuracio/provincia/create'],
            ['/admin/configuracio/provincia/1/edit'],
            ['/admin/configuracio/ciutat/list'],
            ['/admin/configuracio/ciutat/create'],
            ['/admin/configuracio/ciutat/1/edit'],
            ['/admin/configuracio/usuari/list'],
            ['/admin/configuracio/usuari/create'],
            ['/admin/configuracio/usuari/1/edit'],
            ['/admin/configuracio/usuari/1/delete'],
            ['/admin/configuracio/series-factura/list'],
            ['/admin/configuracio/series-factura/create'],
            ['/admin/configuracio/series-factura/1/edit'],
            ['/admin/configuracio/series-factura/1/delete'],
            ['/admin/configuracion/franjas_horarias/list'],
        ];
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
        return [
            // Web
            ['/admin/web/servei/1/show'],
            ['/admin/web/servei/batch'],
            ['/admin/web/servei/1/delete'],
            ['/admin/web/treball/1/show'],
            ['/admin/web/treball/batch'],
            ['/admin/web/treball/1/delete'],
            ['/admin/web/imatge-treball/1/show'],
            ['/admin/web/imatge-treball/batch'],
            ['/admin/web/accesori/1/show'],
            ['/admin/web/accesori/batch'],
            ['/admin/web/accesori/1/delete'],
            ['/admin/web/missatge-contacte/create'],
            ['/admin/web/missatge-contacte/1/edit'],
            ['/admin/web/missatge-contacte/1/delete'],
            // Operator
            ['/admin/operaris/operari/1/delete'],
            ['/admin/operaris/operari/1/show'],
            ['/admin/operaris/operari/batch'],
            ['/admin/operaris/tipus-revisio/1/delete'],
            ['/admin/operaris/tipus-revisio/1/show'],
            ['/admin/operaris/tipus-revisio/batch'],
            ['/admin/operaris/revisio/1/delete'],
            ['/admin/operaris/revisio/1/show'],
            ['/admin/operaris/revisio/batch'],
            ['/admin/operaris/tipus-absencia/1/delete'],
            ['/admin/operaris/tipus-absencia/1/show'],
            ['/admin/operaris/tipus-absencia/batch'],
            ['/admin/operaris/absencia/1/delete'],
            ['/admin/operaris/absencia/1/show'],
            ['/admin/operaris/absencia/batch'],
            ['/admin/operaris/tacograf/1/delete'],
            ['/admin/operaris/tacograf/1/show'],
            ['/admin/operaris/tacograf/batch'],
            ['/admin/operaris/imports-varis/1/show'],
            ['/admin/operaris/imports-varis/batch'],
            // Vehicle
            ['/admin/vehicles/vehicle/1/show'],
            ['/admin/vehicles/vehicle/batch'],
            ['/admin/vehicles/vehicle/1/delete'],
            ['/admin/vehicles/categoria-vehicle/1/show'],
            ['/admin/vehicles/categoria-vehicle/batch'],
            ['/admin/vehicles/categoria-vehicle/1/delete'],
            ['/admin/vehicles/revisio/1/delete'],
            ['/admin/vehicles/revisio/1/show'],
            ['/admin/vehicles/revisio/batch'],
            ['/admin/vehicles/tipus-revisio/1/delete'],
            ['/admin/vehicles/tipus-revisio/1/show'],
            ['/admin/vehicles/tipus-revisio/batch'],
            ['/admin/vehicles/tacograf/delete'],
            ['/admin/vehicles/tacograf/show'],
            ['/admin/vehicles/tacograf/1/batch'],
            // Partner
            ['/admin/tercers/classe/1/show'],
            ['/admin/tercers/classe/batch'],
            ['/admin/tercers/tipus/1/show'],
            ['/admin/tercers/tipus/batch'],
            ['/admin/tercers/tercer/1/show'],
            ['/admin/tercers/tercer/batch'],
            ['/admin/tercers/comandes/1/show'],
            ['/admin/tercers/comandes/batch'],
            ['/admin/tercers/obres/1/show'],
            ['/admin/tercers/obres/batch'],
            ['/admin/tercers/contacte/1/show'],
            ['/admin/tercers/contacte/batch'],
            ['/admin/tercers/dies-inhabils/1/show'],
            ['/admin/tercers/dies-inhabils/batch'],
            // Enterprise
            ['/admin/empreses/empresa/1/show'],
            ['/admin/empreses/empresa/batch'],
            ['/admin/empreses/empresa/1/delete'],
            ['/admin/empreses/grup-prima/1/show'],
            ['/admin/empreses/grup-prima/batch'],
            ['/admin/empreses/compte-bancari/1/show'],
            ['/admin/empreses/compte-bancari/batch'],
            ['/admin/empreses/dies-festius/1/show'],
            ['/admin/empreses/dies-festius/batch'],
            ['/admin/empreses/linies-activitat/1/show'],
            ['/admin/empreses/linies-activitat/batch'],
            ['/admin/empreses/tipus-document-cobrament/1/show'],
            ['/admin/empreses/tipus-document-cobrament/batch'],
            // Sale
            ['/admin/vendes/tarifa/1/show'],
            ['/admin/vendes/tarifa/batch'],
            ['/admin/vendes/tarifa/1/delete'],
            ['/admin/vendes/peticio/1/show'],
            ['/admin/vendes/albara/1/show'],
            ['/admin/vendes/albara/batch'],
            ['/admin/vendes/albara-linia/1/show'],
            ['/admin/vendes/albara-linia/batch'],
            ['/admin/vendes/factura/1/show'],
            ['/admin/vendes/factura/1/delete'],
            ['/admin/vendes/factura/batch'],
            ['/admin/vendes/valoracio-peticio-albara/1/show'],
            ['/admin/vendes/valoracio-peticio-albara/batch'],
            // Setting
            ['/admin/configuracio/provincia/1/show'],
            ['/admin/configuracio/provincia/batch'],
            ['/admin/configuracio/provincia/1/delete'],
            ['/admin/configuracio/ciutat/1/show'],
            ['/admin/configuracio/ciutat/batch'],
            ['/admin/configuracio/ciutat/1/delete'],
            ['/admin/configuracio/usuari/1/show'],
            ['/admin/configuracio/usuari/batch'],
            ['/admin/configuracio/series-factura/1/show'],
            ['/admin/configuracio/series-factura/batch'],
        ];
    }
}
