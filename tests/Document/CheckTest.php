<?php


namespace Dokobit\Tests\Document;

use Dokobit\Document\Check;
use Dokobit\QueryInterface;
use Dokobit\Tests\TestCase;

class CheckTest extends TestCase
{
    const TYPE = 'pdf';
    const NAME = 'document.pdf';

    /** @var  Check */
    private $query;

    public function setUp()
    {
        $this->query = new Check(
            self::TYPE,
            __DIR__.'/../data/document.pdf'
        );
    }

    public function testGetFields()
    {
        $fields = $this->query->getFields();

        $this->assertArrayHasKey('type', $fields);
        $this->assertArrayHasKey('file', $fields);
        $this->assertArrayHasKey('name', $fields['file']);
        $this->assertArrayHasKey('digest', $fields['file']);
        $this->assertArrayHasKey('content', $fields['file']);

        $this->assertSame(self::TYPE, $fields['type']);
        $this->assertSame(self::NAME, $fields['file']['name']);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage File "" does not exist
     */
    public function testGetFileFieldsWithNonExistingFile()
    {
        $method = new Check(self::TYPE, null);
        $method->getFields();
    }

    public function testGetAction()
    {
        $this->assertSame('check', $this->query->getAction());
    }

    public function testGetMethod()
    {
        $this->assertSame(QueryInterface::POST, $this->query->getMethod());
    }

    public function testCreateResult()
    {
        $this->assertInstanceOf('Dokobit\Document\CheckResult', $this->query->createResult());
    }

    public function testHasValidationConstraints()
    {
        $collection = $this->query->getValidationConstraints();

        $this->assertInstanceOf(
            'Symfony\Component\Validator\Constraints\Collection',
            $collection
        );
    }
}
