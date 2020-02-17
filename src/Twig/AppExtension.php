<?php

namespace App\Twig;

use App\Entity\Operator\Operator;
use App\Entity\Setting\User;
use App\Enum\UserRolesEnum;
use App\Repository\Web\ContactMessageRepository;
use DateTime;
use DateTimeInterface;
use Exception;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

/**
 * Class AppExtension.
 *
 * @category Twig
 */
class AppExtension extends AbstractExtension
{
    /**
     * @var RouterInterface
     */
    private RouterInterface $rs;

    /**
     * @var UploaderHelper
     */
    private UploaderHelper $vuhs;

    /**
     * @var CacheManager
     */
    private CacheManager $licms;

    /**
     * @var ContactMessageRepository
     */
    private ContactMessageRepository $cmrs;

    /**
     * Methods.
     */

    /**
     * AppExtension constructor.
     *
     * @param RouterInterface          $rs
     * @param UploaderHelper           $vuhs
     * @param CacheManager             $licms
     * @param ContactMessageRepository $cmrs
     */
    public function __construct(RouterInterface $rs, UploaderHelper $vuhs, CacheManager $licms, ContactMessageRepository $cmrs)
    {
        $this->rs = $rs;
        $this->vuhs = $vuhs;
        $this->licms = $licms;
        $this->cmrs = $cmrs;
    }

    /**
     * Twig Functions.
     */

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new TwigFunction('randomErrorText', array($this, 'randomErrorTextFunction')),
            new TwigFunction('showUnreadMessages', array($this, 'showUnreadMessages')),
        );
    }

    /**
     * @param int $length length of Random String returned
     *
     * @return string
     */
    public function randomErrorTextFunction($length = 1024)
    {
        // character List to Pick from
        $chrList = '012 3456 789 abcdef ghij klmno pqrs tuvwxyz ABCD EFGHIJK LMN OPQ RSTU VWXYZ';
        // minimum/maximum times to repeat character List to seed from
        $chrRepeatMin = 1; // minimum times to repeat the seed string
        $chrRepeatMax = 30; // maximum times to repeat the seed string

        return substr(str_shuffle(str_repeat($chrList, mt_rand($chrRepeatMin, $chrRepeatMax))), 1, $length);
    }

    /**
     * @return string
     *
     * @throws \Exception
     */
    public function showUnreadMessages()
    {
        $result = '';
        if ($this->cmrs->getReadPendingMessagesAmount() > 0) {
            $result = '<li class="messages-menu"><a href="'.$this->rs->generate('admin_app_web_contactmessage_list').'"><i class="fa fa-envelope-o"></i><span class="label label-danger">'.$this->cmrs->getReadPendingMessagesAmount().'</span></a></li>';
        }

        return $result;
    }

    /**
     * Twig Filters.
     */

    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new TwigFilter('draw_operator_image_src', array($this, 'drawOperatorImageSrc')),
            new TwigFilter('draw_operator_image', array($this, 'drawOperatorImage')),
            new TwigFilter('draw_role_span', array($this, 'drawRoleSpan')),
            new TwigFilter('age', array($this, 'ageCalculate')),
        );
    }

    /**
     * @param Operator $operator
     * @param string   $mapping
     * @param string   $filter
     *
     * @return string
     */
    public function drawOperatorImageSrc(Operator $operator, $mapping = 'profilePhotoImageFile', $filter = '60x60')
    {
        if ($operator->getProfilePhotoImage()) {
            $result = $this->licms->getBrowserPath($this->vuhs->asset($operator, $mapping), $filter);
        } else {
            $result = 'https://via.placeholder.com/60x60.png?text='.$operator->getUppercaseNameInitials();
        }

        return $result;
    }

    /**
     * @param Operator $operator
     * @param string   $mapping
     * @param string   $filter
     *
     * @return string
     */
    public function drawOperatorImage(Operator $operator, $mapping = 'profilePhotoImageFile', $filter = '60x60')
    {
        if ($operator->getProfilePhotoImage()) {
            $result = '<img src="'.$this->licms->getBrowserPath($this->vuhs->asset($operator, $mapping), $filter).'" alt="'.$operator->getFullName().' thumbnail">';
        } else {
            $result = '<img src="https://via.placeholder.com/60x60.png?text='.$operator->getUppercaseNameInitials().'" alt="'.$operator->getFullName().' thumbnail">';
        }

        return $result;
    }

    /**
     * @param User $object
     *
     * @return string
     */
    public function drawRoleSpan($object)
    {
        $span = '';
        if ($object instanceof User && count($object->getRoles()) > 0) {
            /** @var string $role */
            foreach ($object->getRoles() as $role) {
                if (UserRolesEnum::ROLE_CMS == $role) {
                    $span .= '<span class="label label-success" style="margin-right:10px">editor</span>';
                } elseif (UserRolesEnum::ROLE_MANAGER == $role) {
                    $span .= '<span class="label label-warning" style="margin-right:10px">gestor</span>';
                } elseif (UserRolesEnum::ROLE_ADMIN == $role) {
                    $span .= '<span class="label label-primary" style="margin-right:10px">administrador</span>';
                } elseif (UserRolesEnum::ROLE_SUPER_ADMIN == $role) {
                    $span .= '<span class="label label-danger" style="margin-right:10px">superadministrador</span>';
                }
            }
        } else {
            $span = '<span class="label label-success" style="margin-right:10px">---</span>';
        }

        return $span;
    }

    /**
     * @param DateTimeInterface $birthday
     *
     * @return int
     *
     * @throws Exception
     */
    public function ageCalculate(DateTimeInterface $birthday)
    {
        $now = new DateTime();
        $interval = $now->diff($birthday);

        return $interval->y;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_extension';
    }
}
