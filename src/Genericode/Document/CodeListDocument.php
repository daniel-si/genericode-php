<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 13.03.19
 * Time: 07:18
 */

namespace DanielSi\Genericode\Document;


class CodeListDocument
{
    private $metadata;

    /**
     * @var Column[]
     */
    private $columns;

    /**
     * @var Row[]
     */
    private $rows;

    protected function __construct()
    {
        $this->columns = [];
        $this->rows = [];
    }

    public function hasColumn($column)
    {
        return null !== $this->getColumn($column);
    }

    public function getColumn($column)
    {
        if ($column instanceof Column) {
            return (false !== array_search($column, $this->columns, true)) ? $column : null;
        }

        if (is_int($column)) {
            return ($column >= 0 && $column < count($this->columns)) ? $this->columns[$column] : null;
        }

        if (is_string($column)) {
            foreach ($this->columns as $col) {
                if ($col->getId() == $column) {
                    return $col;
                }
            }
            return null;
        }

        throw new \InvalidArgumentException('Argument column must be string or integer');
    }

    public function countColumns()
    {
        return count($this->columns);
    }

    public function getRow($index)
    {
        if (!isset($this->rows[$index])) {
            throw new \InvalidArgumentException('Index out of bounds.');
        }

        return $this->rows[$index];
    }

    public function countRows()
    {
        return count($this->rows);
    }

    /**
     * @param $filename
     * @return CodeListDocument
     * @throws \Exception
     */
    public static function loadFromXML($filename)
    {
        $doc = new \DOMDocument();
        $doc->load($filename);

        // FIXME: Validate
        // FIXME: Check for contents that is not supported

        $gc = new self();

        foreach($doc->getElementsByTagName('Column') as $columnData) {
            $gc->columns[] = Column::loadFromXML($columnData);
        }

        foreach($doc->getElementsByTagName('Row') as $rowData) {
            $gc->rows[] = Row::loadFromXML($gc, $rowData);
        }

        return $gc;
    }
}