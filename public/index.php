<?php

/**
 * @see https://npowest.ru
 *
 * @license Shareware
 * @copyright (c) 2019-2024 NPOWest
 */

declare(strict_types=1);

use App\Core\Controller;
use Npowest\Modbus\Modbus;
use Symfony\Component\HttpFoundation\{Request, Response};
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once '../vendor/autoload.php';
// composer autoload

$request = Request::createFromGlobals();

$address   = $request->request->getString('ip', '');
$port      = $request->request->getInt('port', 0);
$mbAddress = $request->request->getInt('mbAddress', 0);
$key       = $request->request->getInt('key', 0);
$type      = $request->request->getString('type', '');

$msg = sprintf('42%02x', $key);

$status = Response::HTTP_OK;
$lsdMsg = '';
if ($address && $port && method_exists(Controller::class, $type))
{
    $modbus     = new Modbus();
    $controller = new Controller();

    $modbus->setAddress($address, $port, $mbAddress);

    $modbus->setMsg($msg);

    $result = $modbus->app();

    if (! $result)
    {
        $lsdMsg = [['string' => 'Ошибка при получении данных', 'code' => 0]];
        $status = Response::HTTP_GATEWAY_TIMEOUT;
    }
    else
    {
        $lsdMsg = $controller->{$type}($result);
    }
}

$loader = new FilesystemLoader('../templates');
$twig   = new Environment(
    $loader,
    [
        'cache' => '../cache',
    ]
);

$template = ($request->isXmlHttpRequest()) ? 'lsd.html' : 'index.html';

$content = $twig->render(
    $template,
    [
        'address'   => $address,
        'port'      => $port,
        'mbAddress' => $mbAddress,
        'key'       => $key,
        'lsdMsg'    => $lsdMsg,
        'type'      => $type,
    ]
);

$response = new Response(
    $content,
    $status,
    ['content-type' => 'text/html']
);
$response->send();
