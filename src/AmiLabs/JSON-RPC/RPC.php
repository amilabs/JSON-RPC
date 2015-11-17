<?php
/**
 * @package AmiLabs/JSON-RPC
 */

namespace AmiLabs\JSON-RPC;

use InvalidArgumentException;
use RuntimeException;

/**
 * Remote Procedure Call client/server implementation.
 *
 * Factory, returnining RPC layers.
 * Example:
 * <code>
 * use AmiLabs\JSON-RPC\RPC;
 *
 * $client = RPC::getLayer(
 *     'JSON',
 *     // or class implementing AmiLabs\JSON-RPC\RPC\ClientInterface interface:
 *     // '\\My\\Namespace\\JSON',
 *     RPC::TYPE_CLIENT,
 *     array(
 *         CURLOPT_SSL_VERIFYPEER => FALSE,
 *         CURLOPT_SSL_VERIFYHOST => FALSE
 *     )
 * );
 * $client->open('https://user:password@domain:port/');
 * $response = $client->execute(
 *     'methodName',
 *     array(
 *         'param1' => 'value1',
 *         'param2' => 'value2',
 *         // ...
 *     )
 * );
 * </code>
 *
 * @package AmiLabs/JSON-RPC
 * @author  deepeloper ({@see https://github.com/deepeloper})
 */
class RPC
{
    const TYPE_CLIENT = 1;
    const TYPE_SERVER = 2;

    /**
     * Returns RPC layer.
     *
     * @param  string $layer    RPC layer, for exaple 'JSON'
     * @param  int    $type     self::TYPE_CLIENT | self::TYPE_SERVER
     * @param  array  $options  Options passing to the layer
     * @return \AmiLabs\JSON-RPC\RPC\Layer
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public static function getLayer($layer, $type, array $options = array())
    {
        switch ($type) {
            case self::TYPE_CLIENT:
                $type = 'Client';
                break;
            case self::TYPE_SERVER:
                $type = 'Server';
                break;
            default:
                throw new InvalidArgumentException(
                    sprintf('Invalid layer type %s', $type)
                );
        }
        if (FALSE === mb_strpos($layer, '\\', NULL, 'ASCII')) {
            $class = "AmiLabs\\JSON-RPC\\RPC\\{$type}\\{$layer}";
        } else {
            $class = $layer;
        }
        $layer = new $class($options);
        $interface = "AmiLabs\\JSON-RPC\\RPC\\{$type}Layer";
        if (!($layer instanceof $interface)) {
            throw new RuntimeException(
                sprintf('Class %s does not implement %s interface', $class, $interface)
            );
        }

        return $layer;
    }
}
