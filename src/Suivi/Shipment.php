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
use InvalidArgumentException;

/**
 * Class Shipment
 *
 * @package LaPoste\Suivi
 */
class Shipment
{

    /** @var string */
    protected $id_ship;

    /** @var string */
    protected $url_detail;

    /** @var int */
    protected $holder;

    /** @var string */
    protected $product;

    /** @var bool */
    protected $is_final;

    /** @var DateTime */
    protected $entry_date;
    
    /** @var DateTime */
    protected $estim_date;

    /** @var DateTime */
    protected $delivery_date;

    /** @var Timeline[] */
    protected $timeline;

    /** @var Event[] */
    protected $event;

    /** @var ContextData */
    protected $context_data;

    /** @var string */
    protected $url;

    /**
     * @return string
     */
    public function getIdShip()
    {
        return $this->id_ship;
    }

    /**
     * @param string $id_ship
     */
    public function setIdShip($id_ship)
    {
        $this->id_ship = $id_ship;
    }

    /**
     * @return string
     */
    public function getUrlDetail()
    {
        return $this->url_detail;
    }

    /**
     * @param string $url_detail
     */
    public function setUrlDetail($url_detail)
    {
        $this->url_detail = $url_detail;
    }

    /**
     * @return int
     */
    public function getHolder()
    {
        return $this->holder;
    }

    /**
     * @param int $holder
     */
    public function setHolder($holder)
    {
        $this->holder = $holder;
    }

    /**
     * @return string
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param string $product
     */
    public function setProduct($product)
    {
        $this->product = $product;
    }

    /**
     * @return bool
     */
    public function isIsFinal()
    {
        return $this->is_final;
    }

    /**
     * @param bool $is_final
     */
    public function setIsFinal($is_final)
    {
        $this->is_final = $is_final;
    }

    /**
     * @return DateTime
     */
    public function getEntryDate()
    {
        return $this->entry_date;
    }

    /**
     * @param string $entry_date
     * @throws Exception
     */
    public function setEntryDate($entry_date)
    {
        if (is_string($entry_date) && strlen($entry_date)) {
            $entry_date = new DateTime($entry_date);
        }

        $this->entry_date = $entry_date;
    }
    
    /**
     * @return DateTime
     */
    public function getEstimDate()
    {
        return $this->estim_date;
    }
    
    /**
     * @param string $estim_date
     * @throws Exception
     */
    public function setEstimDate($estim_date)
    {
        if (is_string($estim_date) && strlen($estim_date)) {
            $estim_date = new DateTime($estim_date);
        }

        if (!$estim_date instanceof DateTime) {
            throw new InvalidArgumentException(sprintf(
                'Expected a string or an instance of DateTime, "%s" provided...',
                is_object($estim_date) ? get_class($estim_date) : gettype($estim_date)
            ));
        }
        
        $this->estim_date = $estim_date;
    }

    /**
     * @return DateTime
     */
    public function getDeliveryDate()
    {
        return $this->delivery_date;
    }

    /**
     * @param string $delivery_date
     * @throws Exception
     */
    public function setDeliveryDate($delivery_date)
    {
        if (is_string($delivery_date) && strlen($delivery_date)) {
            $delivery_date = new DateTime($delivery_date);
        }

        $this->delivery_date = $delivery_date;
    }

    /**
     * @return Timeline[]
     */
    public function getTimeline()
    {
        return $this->timeline;
    }

    /**
     * @param array $data
     */
    public function setTimeline($data)
    {
        $timelines = [];

        foreach ($data as $row) {
            $timeline = new Timeline();

            foreach ($row as $parameter => $value) {
                $timeline->{'set'.$parameter}($value);
            }

            $timelines[] = $timeline;
        }

        $this->timeline = $timelines;
    }

    /**
     * @return Event[]
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param array $data
     */
    public function setEvent($data)
    {
        $events = [];

        foreach ($data as $row) {
            $event = new Event();

            foreach ($row as $parameter => $value) {
                $event->{'set'.$parameter}($value);
            }

            $events[] = $event;
        }

        $this->event = $events;
    }

    /**
     * @return ContextData
     */
    public function getContextData()
    {
        return $this->context_data;
    }

    /**
     * @param array $data
     */
    public function setContextData($data)
    {
        $context_data = new ContextData();
        foreach ($data as $parameter => $value) {
            $context_data->{'set'.ucfirst($parameter)}($value);
        }

        $this->context_data = $context_data;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }
}
