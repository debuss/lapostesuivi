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
 * Interface ApplicationInterface
 * @package LaPoste\Suivi
 */
interface ApplicationInterface
{

    /**
     * @param Request $request
     * @return Response|mixed
     * @throws ResponseDecodeException
     */
    public function call(Request $request);

    /**
     * @param Request[] $requests
     * @return Response[]|mixed
     * @throws ResponseDecodeException
     */
    public function callMultiple($requests);
}
