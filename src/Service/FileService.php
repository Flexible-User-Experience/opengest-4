<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

/**
 * Class FileService.
 *
 * @category Service
 */
class FileService
{
    private UploaderHelper $uhs;

    private ContainerBagInterface $containerBag;

    /**
     * Methods.
     */
    public function __construct(UploaderHelper $uhs, ContainerBagInterface $containerBag)
    {
        $this->uhs = $uhs;
        $this->containerBag = $containerBag;
    }

    /**
     * @return UploaderHelper
     */
    public function getUhs(): UploaderHelper
    {
        return $this->uhs;
    }

    /**
     * @param mixed  $entity
     * @param string $attribute
     *
     * @return string
     */
    public function getMimeType($entity, $attribute): string
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $path = $this->containerBag->get('kernel.project_dir').DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'public'.$this->uhs->asset($entity, $attribute);
        $mimeType = finfo_file($finfo, $path);
        finfo_close($finfo);

        return $mimeType;
    }

    /**
     * @param mixed  $entity
     * @param string $attribute
     *
     * @return bool
     */
    public function isImage($entity, $attribute): bool
    {
        if ('image/jpg' == $this->getMimeType($entity, $attribute) || 'image/jpeg' == $this->getMimeType($entity, $attribute) || 'image/png' == $this->getMimeType($entity, $attribute) || 'image/gif' == $this->getMimeType($entity, $attribute)) {
            return true;
        }

        return false;
    }

    /**
     * @param mixed  $entity
     * @param string $attribute
     *
     * @return bool
     */
    public function isPdf($entity, $attribute): bool
    {
        if ('application/pdf' == $this->getMimeType($entity, $attribute) || 'application/x-pdf' == $this->getMimeType($entity, $attribute)) {
            return true;
        }

        return false;
    }
}
