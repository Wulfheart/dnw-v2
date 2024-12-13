<?php

namespace Dnw\Game\Test\Feature\Fake;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Symfony\Component\Filesystem\Path;

final readonly class FakeClient implements ClientInterface
{
    public function __construct(
        private ClientInterface $realClient,
        private string $fixturePath,
    ) {}

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $hash = md5($request->getBody()->getContents());
        $path = Path::join($this->fixturePath, $hash . '.response');
        if (! file_exists($path)) {
            $response = $this->realClient->sendRequest($request);
            file_put_contents($path, serialize($response));

            return $response;
        }
        $data = file_get_contents($path);
        // @codeCoverageIgnoreStart
        if ($data === false) {
            throw new RuntimeException("Failed to read response from $path");
        }

        // @codeCoverageIgnoreEnd
        return unserialize($data);
    }
}
