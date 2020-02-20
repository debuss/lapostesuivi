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
 * Class Event
 *
 * @package LaPoste\Suivi
 */
class Event
{

    /** @var int */
    protected $order;

    /** @var string */
    protected $label;

    /** @var DateTime */
    protected $date;

    /** @var string */
    protected $code;

    /**
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param int $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
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
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }
}
