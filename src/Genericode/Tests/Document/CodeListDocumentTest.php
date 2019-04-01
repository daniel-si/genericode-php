<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 19.03.19
 * Time: 07:41
 */

namespace DanielSi\Genericode\Tests\Document;

use DanielSi\Genericode\Document\CodeListDocument;
use PHPUnit\Framework\TestCase;

class CodeListDocumentTest extends TestCase
{
    public function testLoadFromXML()
    {
        $doc = CodeListDocument::loadFromXML(__DIR__ . DIRECTORY_SEPARATOR . '..'. DIRECTORY_SEPARATOR . 'simple-valid.xml');

        $this->assertEquals('id', $doc->getColumn(0)->getName());
        $this->assertEquals('ISO_3', $doc->getColumn(1)->getName());
        $this->assertEquals('ISO_2', $doc->getColumn(2)->getName());

        $this->assertEquals(true, $doc->getColumn(0)->isRequired());
        $this->assertEquals(false, $doc->getColumn(1)->isRequired());
        $this->assertEquals(false, $doc->getColumn(2)->isRequired());

        $this->assertEquals('1', $doc->getRow(0)->get('id'));
        $this->assertEquals('BIH', $doc->getRow(1)->get('ISO_3'));
        $this->assertEquals('AD', $doc->getRow(2)->get('ISO_2'));
        $this->assertNull($doc->getRow(2)->get('ISO_3'));
    }
}
