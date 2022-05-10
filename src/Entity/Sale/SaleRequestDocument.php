<?php

namespace App\Entity\Sale;

use App\Entity\AbstractBase;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use DateTime;

/**
 * Class SaleRequestDocument.
 *
 * @category Entity
 *
 * @author Jordi Sort <jordi.sort@mirmit.com>
 *
 * @ORM\Entity(repositoryClass="App\Repository\Sale\SaleRequestRepository")
 * @ORM\Table(name="sale_request_document")
 * @UniqueEntity({"description"})
 */
class SaleRequestDocument extends AbstractBase
{

}
