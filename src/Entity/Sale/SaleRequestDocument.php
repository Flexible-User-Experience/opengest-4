<?php

namespace App\Entity\Sale;

use App\Entity\AbstractBase;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use DateTime;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class SaleRequestDocument.
 *
 * @category Entity
 *
 * @author Jordi Sort <jordi.sort@mirmit.com>
 *
 * @ORM\Entity(repositoryClass="App\Repository\Sale\SaleRequestDocumentRepository")
 * @ORM\Table(name="sale_request_document")
 * @UniqueEntity({"document"})
 * @Vich\Uploadable()
 */
class SaleRequestDocument extends AbstractBase
{
    /**
     * @var SaleRequest
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Sale\SaleRequest", inversedBy="documents")
     */
    private SaleRequest $saleRequest;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="saleRequestDocument", fileNameProperty="document")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private File $documentFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private string $document;

    /**
     * Methods
     */

    /**
     * @return SaleRequest
     */
    public function getSaleRequest(): SaleRequest
    {
        return $this->saleRequest;
    }

    /**
     * @param SaleRequest $saleRequest
     *
     * @return $this
     */
    public function setSaleRequest($saleRequest): SaleRequestDocument
    {
        $this->saleRequest = $saleRequest;

        return $this;
    }

    /**
     * @return File
     */
    public function getDocumentFile(): File
    {
        return $this->documentFile;
    }

    /**
     * @return SaleRequestDocument
     *
     * @throws Exception
     */
    public function setDocumentFile(File $documentFile = null): SaleRequestDocument
    {
        $this->documentFile = $documentFile;
        if ($documentFile) {
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getDocument(): string
    {
        return $this->document;
    }

    /**
     * @param string $document
     *
     * @return SaleRequestDocument
     */
    public function setDocument(string $document): SaleRequestDocument
    {
        $this->document = $document;

        return $this;
    }

    public function __toString()
    {
        return $this->getDocument();
    }
}
