<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 13.03.19
 * Time: 07:19
 */

namespace DanielSi\Genericode\Document;


class Column
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string[]
     */
    private $longNames;

    /**
     * @var bool
     */
    private $required;

    protected function __construct()
    {
        $this->longNames = [];
    }

    /**
     * @param \DOMElement $columnData
     * @return Column
     */
    public static function loadFromXML(\DOMElement $columnData)
    {
        $column = new Column();

        $column->id = $columnData->getAttribute('Id');
        $column->name = $columnData->getElementsByTagName('ShortName')->item(0)->textContent;
        foreach($columnData->getElementsByTagName('LongName') as $longName) {
            $column->longNames[] = $longName->textContent;
        }
        $column->required = ($columnData->getAttribute('Use') === 'required');

        return $column;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function getLongNames()
    {
        return $this->longNames;
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return $this->required;
    }
}