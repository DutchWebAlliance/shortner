<?php

use DWA\Shortner\Exception\IncorrectKeyException;
use DWA\Shortner\Link;
use DWA\Shortner\Shortner;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

require __DIR__ . "/../vendor/autoload.php";

$shortner = new Shortner(new \DWA\Shortner\Storage\RedisStorage());

$request = Request::createFromGlobals();
$path = explode('/', $request->getPathInfo());
$path = array_shift(array_filter($path));
$domain = $request->get('link', '');
$key = $request->get('key', '');

if ($request->isMethod('POST')) {
    $response = processPost($shortner, $path, $domain, $key);
} elseif ($request->isMethod('POST')) {
    $response = processGet($shortner, $path);
} else {
    $response = new Response('Unsupported method', Response::HTTP_METHOD_NOT_ALLOWED);
}

$response->send();
exit;


/**
 * @param Shortner $shortner
 * @param string $path
 * @return Response
 */
function processGet(Shortner $shortner, $path)
{
    $link = $shortner->findLink($path);

    if ($link) {
        // Found, so increase hitcount
        $shortner->increaseHitCount($link);
    } else {
        // Not found, so use default redirection
        $link = new Link($path, 'https://dutchweballiance.nl', '', 0);
    }

    $headers = array(
        'Location' => $link->getDomain(),
        'X-Hits' => $link->gethits(),
    );
    return new Response('', Response::HTTP_SEE_OTHER, $headers);
}

/**
 * @param Shortner $shortner
 * @param string $path
 * @param string $domain
 * @param string $key
 * @return Response
 */
function processPost(Shortner $shortner, $path, $domain, $key) {
    try {
        $link = $shortner->shorten($path, $domain, $key);
    } catch (IncorrectKeyException $e) {
        // Custom exception
        return new Response($e->getMessage(), Response::HTTP_UNAUTHORIZED);
    } catch (\LogicException $e) {
        // Logic exceptions are users fault
        return new Response($e->getMessage(), Response::HTTP_BAD_REQUEST);
    } catch (\Exception $e) {
        // Runtime and all other exceptions are our fault
        return new Response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    $headers = array(
        'x-shortner-path' => $link->getPath(),
        'x-shortner-domain' => $link->getDomain(),
        'x-shortner-key' => $link->getKey(),
    );
    return new Response('Link created or updated', Response::HTTP_CREATED, $headers);
}
