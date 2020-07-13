<?php

namespace App\Service;

use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

/**
 * Class FileService.
 *
 * @category Service
 */
class FileService
{
    /**
     * @var UploaderHelper
     */
    private UploaderHelper $uhs;

    /**
     * @var string
     */
    private string $krd;

    /**
     * Methods.
     */

    /**
     * @param UploaderHelper $uhs
     * @param string         $krd
     */
    public function __construct(UploaderHelper $uhs, $krd)
    {
        $this->uhs = $uhs;
        $this->krd = $krd;
    }

    /**
     * @return UploaderHelper
     */
    public function getUhs()
    {
        return $this->uhs;
    }

    /**
     * @param mixed  $entity
     * @param string $attribute
     *
     * @return string
     */
    public function getMimeType($entity, $attribute)
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $path = $this->krd.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'public'.$this->uhs->asset($entity, $attribute);
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
    public function isImage($entity, $attribute)
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
    public function isPdf($entity, $attribute)
    {
        if ('application/pdf' == $this->getMimeType($entity, $attribute) || 'application/x-pdf' == $this->getMimeType($entity, $attribute)) {
            return true;
        }

        return false;
    }
}
