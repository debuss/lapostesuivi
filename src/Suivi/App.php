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
use LaPoste\Exception\BadXOkapiKeyException;
use LaPoste\Exception\ResponseDecodeException;

/**
 * Class App
 *
 * @package LaPoste\Suivi
 */
class App implements ApplicationInterface
{

    const ENDPOINT = 'https://api.laposte.fr/suivi/v2/idships/';
    const USER_AGENT = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1';

    const LANG_FR = 'fr_FR';
    const LANG_DE = 'de_DE';
    const LANG_EN = 'en_GB';
    const LANG_ES = 'es_ES';
    const LANG_IT = 'it_IT';
    const LANG_NL = 'nl_NL';

    /** @var string */
    protected $x_okapi_key;

    /**
     * App constructor.
     *
     * @param string $x_okapi_key
     * @throws BadXOkapiKeyException
     */
    public function __construct($x_okapi_key)
    {
        if (!is_string($x_okapi_key) || strlen($x_okapi_key) != 64) {
            throw new BadXOkapiKeyException();
        }

        $this->x_okapi_key = $x_okapi_key;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws ResponseDecodeException
     */
    public function call(Request $request)
    {
        $response = $this->callMultiple([$request]);

        return reset($response);
    }

    /**
     * @param Request[] $requests
     * @return Response[]
     * @throws ResponseDecodeException
     */
    public function callMultiple($requests)
    {
        $multi_curl = [];
        $results = [];

        $curl = curl_multi_init();

        foreach ($requests as $id => $request) {
            if (!$request instanceof Request) {
                throw new InvalidArgumentException(sprintf(
                    'Expected a Request instance, "%s" provided...',
                    is_object($request) ? get_class($request) : gettype($request)
                ));
            }

            $multi_curl[$id] = curl_init();

            curl_setopt($multi_curl[$id], CURLOPT_USERAGENT, self::USER_AGENT);
            curl_setopt($multi_curl[$id], CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($multi_curl[$id], CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($multi_curl[$id], CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($multi_curl[$id], CURLOPT_URL, sprintf(
                '%s%s?lang=%s',
                self::ENDPOINT,
                $request->getId(),
                $request->getLang()
            ));
            curl_setopt($multi_curl[$id], CURLOPT_HTTPHEADER, array(
                'X-Okapi-Key: '.$this->x_okapi_key,
                'Accept: application/json',
                'X-Forwarded-For: '.$request->getIpAddress()
            ));

            curl_multi_add_handle($curl, $multi_curl[$id]);
        }

        $index = null;
        do {
            curl_multi_exec($curl, $index);
        } while ($index > 0);

        foreach ($multi_curl as $id => $ch) {
            $results[$id] = curl_multi_getcontent($ch);

            curl_multi_remove_handle($curl, $ch);
        }

        curl_multi_close($curl);

        return $this->prepareResponse($results);
    }

    /**
     * @param array $results
     * @return array
     * @throws ResponseDecodeException
     */
    protected function prepareResponse($results)
    {
        $responses = [];

        foreach ($results as $id => $result) {
            $result = json_decode($result, true);
            if ($result === null) {
                throw new ResponseDecodeException('Unable to json_decode response from the API.');
            }

            if ($result['code'] === 'SERVICE_UNAVAILABLE') {
                throw new ResponseDecodeException('The service is currently unavailable (SERVICE_UNAVAILABLE).');
            }

            $response = new Response();

            foreach ($result as $parameter => $value) {
                $response->{'set'.ucfirst($parameter)}($value);
            }

            if (!$response->getIdShip() && $response->getReturnCode() == 200) {
                $response->setIdShip($response->getShipment()->getIdShip());
            }

            $responses[$id] = $response;
        }

        return $responses;
    }
}
