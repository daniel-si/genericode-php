<?php
/**
 * Copyright (c) 2019.
 *
 * Daniel Sigg (daniel[dot]sigg[at]fes-ehemalige.de).
 *
 * All rights reserved. Alle Rechts vorbehalten. Lizensiert zur Nutzung und Weiterentwicklung an FES-Ehemalige e.V., Bonn.
 *
 */

/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 21.03.19
 * Time: 13:18
 */

namespace DanielSi\Genericode\Tests\Document;

use DanielSi\Genericode\Document\CodeListDocument;
use DanielSi\Genericode\Document\Column;
use DanielSi\Genericode\Document\Row;
use PHPUnit\Framework\TestCase;

class RowTest extends TestCase
{
    public function testLoadFromXML()
    {
        $columns = [
            [
                'id' => 'id',
                'required' => 'true',
            ],
            [
                'id' => 'ISO_3',
                'required' => 'false',
            ],
            [
                'id' => 'ISO_2',
                'required' => 'false',
            ],
        ];
        $xml = <<<EOF
        <Row>
             <Value ColumnRef="id">
                 <SimpleValue>1</SimpleValue>
             </Value>
             <Value ColumnRef="ISO_3">
                 <SimpleValue>ALB</SimpleValue>
             </Value>
        </Row>
EOF;

        $row = $this->getRow($xml, $this->mockDocument($columns));

        $this->assertEquals('1', $row->get('id'));
        $this->assertEquals('ALB', $row->get('ISO_3'));
        $this->assertNull($row->get('ISO_2'));
    }

    public function testGetInvalidColumn()
    {
        $this->expectException(\Exception::class);

        $xml = <<<EOF
        <Row>
             <Value ColumnRef="id">
                 <SimpleValue>1</SimpleValue>
             </Value>
        </Row>
EOF;

        $document = $this->createMock(CodeListDocument::class);
        $document->method('getColumn')
            ->willReturn(null);

        $row = $this->getRow($xml, $document);
        $row->get('stub');
    }

    private function mockDocument(array $columnData)
    {
        $mock = $this->createMock(CodeListDocument::class);

        $columns = array_map(function ($columnData) {
            $column = $this->createMock(Column::class);
            $column->method('getId')->willReturn($columnData['id']);
            $column->method('isRequired')->willReturn($columnData['required']);

            return $column;
        }, $columnData);

        $mock->method('countColumns')
            ->willReturn(count($columnData));
        $mock->method('getColumn')
            ->will($this->returnCallback(function ($column) use ($columns) {
                if (is_int($column)) {
                    return $columns[$column];
                }

                if (is_string($column)) {
                    $elem = current(array_filter($columns, function($col) use ($column) { return $col->getId() === $column; }));
                    if ($elem !== false) {
                        return $elem;
                    }
                }

                throw new \Exception('Cannot find column');
            }));

        return $mock;
    }

    /**
     * @param $xml
     * @param $document
     * @return Row
     * @throws \Exception
     */
    private function getRow($xml, $document)
    {
        $doc = new \DOMDocument();
        $doc->loadXML($xml);

        $row = Row::loadFromXML($document, $doc->getElementsByTagName('Row')->item(0));

        return $row;
    }
}
