<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 19.03.19
 * Time: 07:41
 */

namespace DanielSi\Genericode\Tests\Document;

use DanielSi\Genericode\Document\CodeListDocument;
use DanielSi\Genericode\Document\Column;
use PHPUnit\Framework\TestCase;

class ColumnTest extends TestCase
{
    public function testColumnFromXML()
    {
        $xml = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<gc:CodeList xmlns:gc="http://docs.oasis-open.org/codelist/ns/genericode/1.0/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <ColumnSet>
        <Column Id="ISO_3_id" Use="required">
            <ShortName>ISO_3</ShortName>
            <LongName>ISO 3 characters</LongName>
            <Data Type="string"/>
        </Column>
    </ColumnSet>
</gc:CodeList>
EOF;

        $doc = new \DOMDocument();
        $doc->loadXML($xml);

        $col = Column::loadFromXML($doc->getElementsByTagName('Column')->item(0));

        $this->assertEquals('ISO_3', $col->getName());
        $this->assertEquals('ISO_3_id', $col->getId());
        $this->assertEquals(true, $col->isRequired());
    }

    public function testColumnFromXMLLongNames()
    {
        $xml = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<ColumnSet>
    <Column Id="ISO_3" Use="optional">
        <ShortName>ISO_3</ShortName>
        <LongName>ISO 3 characters</LongName>
        <LongName>ISO 3 characters 2</LongName>
        <Data Type="string"/>
        <LongName>ISO 3 characters 3</LongName>
    </Column>
</ColumnSet>
EOF;

        $doc = new \DOMDocument();
        $doc->loadXML($xml);

        $col = Column::loadFromXML($doc->getElementsByTagName('Column')->item(0));

        $this->assertEquals(['ISO 3 characters', 'ISO 3 characters 2', 'ISO 3 characters 3'], $col->getLongNames());
    }

    public function testColumnFromXMLNoLongName()
    {
        $xml = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<gc:CodeList xmlns:gc="http://docs.oasis-open.org/codelist/ns/genericode/1.0/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <ColumnSet>
        <Column Id="ISO_3" Use="optional">
            <ShortName>ISO_3</ShortName>
            <Data Type="string"/>
        </Column>
    </ColumnSet>
</gc:CodeList>
EOF;

        $doc = new \DOMDocument();
        $doc->loadXML($xml);

        $col = Column::loadFromXML($doc->getElementsByTagName('Column')->item(0));
        $this->assertIsArray($col->getLongNames());
        $this->assertCount(0, $col->getLongNames());
    }
}