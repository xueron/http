<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Http;

use FastD\Http\Exceptions\RequestException;
use FastD\Http\Factories\RequestFactoryInterface;
use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * Http Request Client
 *
 * Class Request
 *
 * @package FastD\Http
 */
class Request extends Message implements RequestInterface
{
    const USER_AGENT = 'PHP Curl/1.1 (+https://github.com/JanHuang/http)';

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var string
     */
    protected $method = 'GET';

    /**
     * @var string
     */
    protected $requestTarget;

    /**
     * @var Uri
     */
    protected $uri;

    /**
     * Supported HTTP methods
     *
     * @var array
     */
    private $validMethods = [
        'DELETE',
        'GET',
        'HEAD',
        'OPTIONS',
        'PATCH',
        'POST',
        'PUT',
    ];

    /**
     * Request constructor.
     *
     * @param $method
     * @param $uri
     * @param array $headers
     * @param StreamInterface $body
     */
    public function __construct($method, $uri, array $headers = [], StreamInterface $body = null)
    {
        $this->withMethod($method);
        $this->withUri(new Uri($uri));

        foreach ($headers as $key => $header) {
            if (is_array($header)) {
                foreach ($header as $item) {
                    $this->withAddedHeader($key, $item);
                }
            } else {
                $this->withHeader($key, $header);
            }
        }

        parent::__construct($body);
    }

    /**
     * Retrieves the message's request target.
     *
     * Retrieves the message's request-target either as it will appear (for
     * clients), as it appeared at request (for servers), or as it was
     * specified for the instance (see withRequestTarget()).
     *
     * In most cases, this will be the origin-form of the composed URI,
     * unless a value was provided to the concrete implementation (see
     * withRequestTarget() below).
     *
     * If no URI is available, and no request-target has been specifically
     * provided, this method MUST return the string "/".
     *
     * @return string
     */
    public function getRequestTarget()
    {
        return $this->uri->getPath();
    }

    /**
     * Return an instance with the specific request-target.
     *
     * If the request needs a non-origin-form request-target — e.g., for
     * specifying an absolute-form, authority-form, or asterisk-form —
     * this method may be used to create an instance with the specified
     * request-target, verbatim.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * changed request target.
     *
     * @link http://tools.ietf.org/html/rfc7230#section-5.3 (for the various
     *     request-target forms allowed in request messages)
     * @param mixed $requestTarget
     * @return static
     */
    public function withRequestTarget($requestTarget)
    {
        $this->uri->withPath($requestTarget);

        return $this;
    }

    /**
     * Retrieves the HTTP method of the request.
     *
     * @return string Returns the request method.
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Return an instance with the provided HTTP method.
     *
     * While HTTP method names are typically all uppercase characters, HTTP
     * method names are case-sensitive and thus implementations SHOULD NOT
     * modify the given string.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * changed request method.
     *
     * @param string $method Case-sensitive method.
     * @return static
     * @throws InvalidArgumentException for invalid HTTP methods.
     */
    public function withMethod($method)
    {
        $method = strtoupper($method);

        if (!in_array($method, $this->validMethods, true)) {
            throw new InvalidArgumentException(sprintf(
                'Unsupported HTTP method "%s" provided',
                $method
            ));
        }

        $this->method = $method;

        return $this;
    }

    /**
     * Retrieves the URI instance.
     *
     * This method MUST return a UriInterface instance.
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     * @return UriInterface Returns a UriInterface instance
     *     representing the URI of the request.
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Returns an instance with the provided URI.
     *
     * This method MUST update the Host header of the returned request by
     * default if the URI contains a host component. If the URI does not
     * contain a host component, any pre-existing Host header MUST be carried
     * over to the returned request.
     *
     * You can opt-in to preserving the original state of the Host header by
     * setting `$preserveHost` to `true`. When `$preserveHost` is set to
     * `true`, this method interacts with the Host header in the following ways:
     *
     * - If the Host header is missing or empty, and the new URI contains
     *   a host component, this method MUST update the Host header in the returned
     *   request.
     * - If the Host header is missing or empty, and the new URI does not contain a
     *   host component, this method MUST NOT update the Host header in the returned
     *   request.
     * - If a Host header is present and non-empty, this method MUST NOT update
     *   the Host header in the returned request.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new UriInterface instance.
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     * @param UriInterface $uri New request URI to use.
     * @param bool $preserveHost Preserve the original state of the Host header.
     * @return static
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function setOption($key, $value)
    {
        $this->options[$key] = $value;

        return $this;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options)
    {
        $this->options = array_merge($this->options, $options);

        return $this;
    }

    /**
     * @param $username
     * @param $password
     * @return $this
     */
    public function setBasicAuthentication($username, $password)
    {
        $this->setOption(CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $this->setOption(CURLOPT_USERPWD, $username . ':' . $password);

        return $this;
    }

    /**
     * @param $referer
     * @return $this
     */
    public function setReferrer($referer)
    {
        $this->setOption(CURLOPT_REFERER, $referer);

        return $this;
    }

    /**
     * @param array $data
     * @param array $headers
     * @return Response
     */
    public function send(array $data = [], array $headers = [])
    {
        $ch = curl_init();
        $url = (string)$this->uri;

        $data = http_build_query($data);

        if (in_array($this->getMethod(), ['PUT', 'POST', 'DELETE', 'PATCH', 'OPTIONS'])) {
            $this->setOption(CURLOPT_POSTFIELDS, $data);
        } else if (!empty($data)) {
            $concat = '?';
            if (false === strpos($url, '?')) {
                $concat = '&';
            }
            $url .= $concat . $data;
        }

        $this->setOption(CURLOPT_USERAGENT, static::USER_AGENT);
        $this->setOption(CURLOPT_HTTPHEADER, $headers);
        $this->setOption(CURLOPT_URL, $url);
        $this->setOption(CURLOPT_CUSTOMREQUEST, $this->getMethod());
        $this->setOption(CURLINFO_HEADER_OUT, true);
        $this->setOption(CURLOPT_HEADER, true);
        $this->setOption(CURLOPT_RETURNTRANSFER, true);

        foreach ($this->options as $key => $option) {
            curl_setopt($ch, $key, $option);
        }

        $response = curl_exec($ch);
        $errorCode = curl_errno($ch);
        $errorMsg = curl_error($ch);

        if ((strpos($response, "\r\n\r\n") === false) || !($errorCode === 0)) {
            throw new RequestException($errorMsg);
        }

        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        list($responseHeaders, $response) = explode("\r\n\r\n", $response, 2);

        $responseHeaders = preg_split('/\r\n/', $responseHeaders, null, PREG_SPLIT_NO_EMPTY);
        array_shift($responseHeaders);

        $headers = [];
        array_map(function ($headerLine) use (&$headers) {
            list($key, $value) = explode(':', $headerLine);
            $headers[$key] = trim($value);
            unset($headerLine, $key, $value);
        }, $responseHeaders);

        curl_close($ch);

        $this->reset();

        return new Response($response, $statusCode, $headers);
    }

    /**
     * Reset request options and request method.
     *
     * @return void
     */
    public function reset()
    {
        $this->options = [];
        $this->method = 'GET';
    }
}