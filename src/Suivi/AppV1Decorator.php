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

use LaPoste\Exception\ResponseDecodeException;

/**
 * Class AppV1Decorator
 *
 * Use this class instead of App to fetch tracking result formatted as the API v1.
 *
 * @package LaPoste\Suivi
 */
class AppV1Decorator implements ApplicationInterface
{

    /** @var ApplicationInterface */
    protected $app;

    /** @var array Matching between v2 event codes and v1 status */
    protected $status_to_event_codes = [
        'PRIS_EN_CHARGE' => ['DR1', 'PC1', 'PC2'],
        'EN_LIVRAISON' => ['ET1', 'ET2', 'ET3', 'ET4', 'EP1'],
        'EXPEDIE' => ['DO1', 'DO2', 'DO3'],
        'A_RETIRER' => ['AG1'],
        'TRI_EFFECTUE' => [],
        'DISTRIBUE' => ['MD2'],
        'LIVRE' => ['DI1', 'DI2'],
        'DESTINATAIRE_INFORME' => [],
        'RETOUR_DESTINATAIRE' => ['RE1'],
        'ERREUR' => ['PB1', 'ND1'],
        'INCONNU' => []
    ];

    /**
     * AppV1Decorator constructor.
     * @param ApplicationInterface $app
     */
    public function __construct(ApplicationInterface $app)
    {
        $this->app = $app;
    }

    /**
     * @param Request $request
     * @return array
     * @throws ResponseDecodeException
     */
    public function call(Request $request)
    {
        $response = $this->callMultiple([$request]);
        $response = reset($response);
        $response = reset($response);

        return $response;
    }

    /**
     * @param Request[] $requests
     * @return array[]
     * @throws ResponseDecodeException
     */
    public function callMultiple($requests)
    {
        $responses = [];

        foreach ($this->app->callMultiple($requests) as $id => $response) {
            $responses[$id] = $this->getFormattedResponse($response);
        }

        return $responses;
    }

    /**
     * @param Response $response
     * @return array
     */
    protected function getFormattedResponse(Response $response)
    {
        switch ($response->getReturnCode()) {
            case 200:
                $current_event = $response->getCurrentEvent();

                return [
                    'data' => [
                        'code' => $response->getIdShip(),
                        'date' => $current_event->getDate()->format('d/m/Y'),
                        'status' => $this->getResponseStatus($current_event->getCode()),
                        'message' => $current_event->getLabel(),
                        'link' => $response->getTrackingUrl(),
                        'type' => ucfirst($response->getShipment()->getProduct())
                    ]
                ];

            case 400:
                return [
                    'error' => [
                        'code' => 'BAD_REQUEST',
                        'message' => $response->getReturnMessage()
                    ]
                ];

            case 401:
                return [
                    'error' => [
                        'code' => 'UNAUTHORIZED',
                        'message' => $response->getReturnMessage()
                    ]
                ];

            case 404:
                return [
                    'error' => [
                        'code' => 'RESOURCE_NOT_FOUND',
                        'message' => $response->getReturnMessage()
                    ]
                ];

            case 500:
                return [
                    'error' => [
                        'code' => 'INTERNAL_SERVER_ERROR',
                        'message' => $response->getReturnMessage()
                    ]
                ];

            case 504:
                return [
                    'error' => [
                        'code' => 'GATEWAY_TIMEOUT',
                        'message' => $response->getReturnMessage()
                    ]
                ];

            default:
                return [
                    'error' => [
                        'code' => $response->getReturnCode(),
                        'message' => $response->getReturnMessage()
                    ]
                ];
        }
    }

    /**
     * @param string $code
     * @return string
     */
    protected function getResponseStatus($code)
    {
        foreach ($this->status_to_event_codes as $status => $event_codes) {
            if (in_array($code, $event_codes)) {
                return $status;
            }
        }

        return 'INCONNU';
    }
}