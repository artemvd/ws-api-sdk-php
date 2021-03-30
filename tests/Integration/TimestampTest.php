<?php
namespace Dokobit\Tests\Integration;


use Dokobit\Document\Timestamp;
use Dokobit\Document\TimestampResult;
use Dokobit\ResultInterface;

class TimestampTest extends TestCase
{
    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage File "" does not exist
     */
    public function testRequiredFileParameters()
    {
        try {
            $this->client->get(new Timestamp(
                'adoc',
                ''
            ));
        } catch (\Dokobit\Exception\QueryValidator $e) {
            $this->assertCount(3, $e->getViolations());
            return;
        }

        $this->fail('Expected exception was not thrown');
    }

    public function testStatusOk()
    {
        /** @var TimestampResult $result */
        $result = $this->client->get(new Timestamp(
            'adoc',
            __DIR__ . '/../data/signed.adoc'
        ));

        $this->assertSame(ResultInterface::STATUS_OK, $result->getStatus());
    }
}
