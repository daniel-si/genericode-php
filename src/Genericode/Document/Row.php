<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 13.03.19
 * Time: 07:19
 */

namespace DanielSi\Genericode\Document;


class Row
{
    private $data;
    private $document;

    protected function __construct(CodeListDocument $document)
    {
        $this->document = $document;
        $this->data = [];
    }

    public static function loadFromXML(CodeListDocument $document, \DOMElement $rowData)
    {
        $row = new self($document);

        foreach($rowData->getElementsByTagName('Value') as $valueData) {
            /**
             * @var \DOMElement $valueData
             */
            $ref = $valueData->getAttribute('ColumnRef');
            $col = $document->getColumn($ref);
            if (null === $col) {
                throw new \Exception(sprintf(
                    'Column Id %s used in ColumnRef has not been specified.', $ref));
            }

            $valueElem = $valueData->getElementsByTagName('SimpleValue');
            if ($valueElem->count() !== 1) {
                throw new \Exception('Only (one) SimpleValue is supported.');
            }

            $row->data[$col->getId()] = $valueElem->item(0)->textContent;
        }

        return $row;
    }

    public function get($column)
    {
        $col = $this->document->getColumn($column);
        if (null === $col) {
            throw new \InvalidArgumentException('Requested column is not defined in the document');
        }

        return (isset($this->data[$col->getId()])) ? $this->data[$col->getId()] : null;
    }
}