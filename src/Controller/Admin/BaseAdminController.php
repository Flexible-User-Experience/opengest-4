<?php

namespace App\Controller\Admin;

use App\Manager\InvoiceManager;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BaseAdminController.
 *
 * @category Controller
 *
 * @author   David Romaní <david@flux.cat>
 */
abstract class BaseAdminController extends Controller
{
    /**
     * @var InvoiceManager
     */
    protected InvoiceManager $im;

    public function __construct(InvoiceManager $invoiceManager)
    {
        $this->im = $invoiceManager;
    }

    /**
     * @param Request|null $request
     *
     * @return Request
     */
    protected function resolveRequest(Request $request = null)
    {
        if (null === $request) {
            return $this->getRequest();
        }

        return $request;
    }
}
