<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CacheUsage\Test\Unit\Observer;

use Infrangible\CacheUsage\Helper\Data;
use Infrangible\CacheUsage\Model\Cache;
use Infrangible\CacheUsage\Model\Recorder;
use Infrangible\CacheUsage\Model\Summary;
use Infrangible\CacheUsage\Observer\ControllerFrontSendResponseBefore;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use RuntimeException;

/**
 * Tests storing the cache usage and adding the summary to the page.
 */
#[AllowMockObjectsWithoutExpectations]
class ControllerFrontSendResponseBeforeTest extends TestCase
{
    /**
     * Tests that a failure while storing is logged and the page still gets its summary.
     *
     * @return void
     */
    public function testStoringFailureDoesNotBreakThePage(): void
    {
        $recorder = $this->createMock(Recorder::class);
        $recorder->expects($this->once())->method('saveFullPageCache')->with(250)->willThrowException(
            new RuntimeException('Data too long')
        );
        $recorder->expects($this->once())->method('saveBlocks');

        $logging = $this->createMock(LoggerInterface::class);
        $logging->expects($this->once())->method('error')->with($this->stringContains('Data too long'));

        $response = $this->createResponse(200, false);
        $response->expects($this->once())->method('setContent')->with('<html lang="en"></html><pre>summary</pre>');

        $this->createObserver($recorder, $logging)->execute($this->createEvent($this->createRequest(true), $response));
    }

    /**
     * Tests that responses that are not HTML, like JSON, get no summary.
     *
     * @return void
     */
    public function testNoSummaryForOtherContent(): void
    {
        $response = $this->createResponse(200, true);
        $response->expects($this->never())->method('setContent');

        $this->createObserver($this->createStub(Recorder::class), $this->createStub(LoggerInterface::class))
            ->execute($this->createEvent($this->createRequest(true), $response));
    }

    /**
     * Tests that redirects and POST requests are ignored.
     *
     * @return void
     */
    public function testIgnoredResponses(): void
    {
        $recorder = $this->createMock(Recorder::class);
        $recorder->expects($this->never())->method('saveFullPageCache');

        $observer = $this->createObserver($recorder, $this->createStub(LoggerInterface::class));

        $observer->execute($this->createEvent($this->createRequest(true), $this->createResponse(302, false)));
        $observer->execute($this->createEvent($this->createRequest(false), $this->createResponse(200, false)));
    }

    /**
     * Returns the observer with collection and summaries enabled and a measured request of 250 ms.
     *
     * @param Recorder $recorder
     * @param LoggerInterface $logging
     *
     * @return ControllerFrontSendResponseBefore
     */
    private function createObserver(Recorder $recorder, LoggerInterface $logging): ControllerFrontSendResponseBefore
    {
        $helper = $this->createStub(Data::class);
        $helper->method('isFullPageCacheEnabled')->willReturn(true);
        $helper->method('isBlocksEnabled')->willReturn(true);

        $summary = $this->createStub(Summary::class);
        $summary->method('getHtml')->willReturn('<pre>summary</pre>');

        $cache = new Cache();

        $cache->setStarted(1.0);
        $cache->setFinished(1.25);

        return new ControllerFrontSendResponseBefore($helper, $cache, $recorder, $summary, $logging);
    }

    /**
     * Returns the event with request and response.
     *
     * @param Http $request
     * @param HttpResponse $response
     *
     * @return Observer
     */
    private function createEvent(Http $request, HttpResponse $response): Observer
    {
        return new Observer(['event' => new Event(['request' => $request, 'response' => $response])]);
    }

    /**
     * Returns a request that is not sent with AJAX.
     *
     * @param bool $get
     *
     * @return Http
     */
    private function createRequest(bool $get): Http
    {
        $request = $this->createStub(Http::class);
        $request->method('isGet')->willReturn($get);
        $request->method('isHead')->willReturn(false);
        $request->method('isAjax')->willReturn(false);

        return $request;
    }

    /**
     * Returns a response with the status code, an HTML page or JSON.
     *
     * @param int $code
     * @param bool $json
     *
     * @return HttpResponse&MockObject
     */
    private function createResponse(int $code, bool $json): HttpResponse
    {
        $response = $this->createMock(HttpResponse::class);
        $response->method('getHttpResponseCode')->willReturn($code);
        $response->method('getContent')->willReturn('<html lang="en"></html>');
        $response->method('getHeader')->with('Content-Type')->willReturn(
            $this->createContentTypeHeader($json ? 'application/json' : 'text/html; charset=UTF-8')
        );

        return $response;
    }

    /**
     * Returns a header with the value, like the Content-Type header of Laminas.
     *
     * @param string $value
     *
     * @return object
     */
    private function createContentTypeHeader(string $value): object
    {
        return new class ($value) {
            /**
             * @param string $value
             */
            public function __construct(private readonly string $value)
            {
            }

            /**
             * Returns the value of the header.
             *
             * @return string
             */
            public function getFieldValue(): string
            {
                return $this->value;
            }
        };
    }
}
