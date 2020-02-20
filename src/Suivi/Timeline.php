<?php
/**
 * La Poste Suivi API
 *
 * @author debuss-a <zizilex@gmail.com>
 * @copyright 2020 debuss-a
 * @license https://github.com/debuss/LaPosteSuivi/LICENSE.md MIT License
 * @link https://developer.laposte.fr/products/suivi/2
 */

namespace LaPoste\Suivi;

use DateTime;
use Exception;

/**
 * Class Timeline
 *
 * @package LaPoste\Suivi
 */
class Timeline
{

    /** @var string */
    protected $short_label;

    /** @var string */
    protected $long_label;

    /** @var int */
    protected $id;

    /** @var DateTime */
    protected $date;

    /** @var string */
    protected $country;

    /** @var bool */
    protected $status;

    /** @var int */
    protected $type;

    /**
     * @return string
     */
    public function getShortLabel()
    {
        return $this->short_label;
    }

    /**
     * @param string $short_label
     */
    public function setShortLabel($short_label)
    {
        $this->short_label = $short_label;
    }

    /**
     * @return string
     */
    public function getLongLabel()
    {
        return $this->long_label;
    }

    /**
     * @param string $long_label
     */
    public function setLongLabel($long_label)
    {
        $this->long_label = $long_label;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param string $date
     * @throws Exception
     */
    public function setDate($date)
    {
        if (is_string($date) && strlen($date)) {
            $date = new DateTime($date);
        }

        $this->date = $date;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return bool
     */
    public function isStatus()
    {
        return $this->status;
    }

    /**
     * @param bool $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }
}
