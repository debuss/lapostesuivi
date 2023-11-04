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

use InvalidArgumentException;

/**
 * Class Request
 *
 * @package LaPoste\Suivi
 */
class Request
{

    /** @var string */
    protected $id;

    /** @var string */
    protected $lang;

    /** @var string */
    protected $ip_address;

    /**
     * Request constructor.
     *
     * @param string $id
     * @param string $lang
     * @param string $ip_address
     */
    public function __construct($id, $lang = 'fr_FR', $ip_address = null)
    {
        $this->setId($id);
        $this->setLang($lang);
        $this->setIpAddress($ip_address);
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        if (!is_string($id) || strlen($id) < 11 || strlen($id) > 15) {
            throw new InvalidArgumentException('Tracking number (id) must be a string of 11, up to 15, alphanumeric characters.');
        }

        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param string $lang
     */
    public function setLang($lang)
    {
        $allowed_lang_enum = ['fr_FR', 'de_DE', 'en_GB', 'es_ES', 'it_IT', 'nl_NL'];

        if (!in_array($lang, $allowed_lang_enum)) {
            throw new InvalidArgumentException(sprintf(
                'Response language must be one of the following : %s.',
                implode(', ', $allowed_lang_enum)
            ));
        }

        $this->lang = $lang;
    }

    /**
     * @return string
     */
    public function getIpAddress()
    {
        return $this->ip_address;
    }

    /**
     * @param string $ip_address
     */
    public function setIpAddress($ip_address)
    {
        $this->ip_address = filter_var($ip_address, FILTER_VALIDATE_IP) ?:
            filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP) ?:
                '123.123.123.123';;
    }
}
