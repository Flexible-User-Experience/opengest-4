<?php

namespace App\Entity\Setting;

use App\Entity\AbstractBase;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use DateTime;

/**
 * Class Province.
 *
 * @category Entity
 *
 * @author Jordi Sort <jordi.sort@mirmit.com>
 *
 * @ORM\Entity(repositoryClass="App\Repository\Setting\TimeRangeRepository")
 * @ORM\Table(name="time_range")
 * @UniqueEntity({"description"})
 */
class TimeRange extends AbstractBase
{
    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private string $description;


    /**
     * @var DateTime
     *
     * @ORM\Column(type="time")
     */
    private DateTime $start;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="time")
     */
    private DateTime $finish;


    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private int $type = 0;

    /**
     * Methods.
     */

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return TimeRange
     */
    public function setDescription(string $description): TimeRange
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getStart(): DateTime
    {
        return $this->start;
    }

    /**
     * @param DateTime $start
     *
     * @return TimeRange
     */
    public function setStart(DateTime $start): TimeRange
    {
        $this->start = $start;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getFinish(): DateTime
    {
        return $this->finish;
    }

    /**
     * @param DateTime $finish
     *
     * @return TimeRange
     */
    public function setFinish(DateTime $finish): TimeRange
    {
        $this->finish = $finish;

        return $this;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     *
     * @return TimeRange
     */
    public function setType(int $type): TimeRange
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->id ? $this->getDescription().' · '.$this->getStart()->format('H:i:s') : '---';
    }
}
