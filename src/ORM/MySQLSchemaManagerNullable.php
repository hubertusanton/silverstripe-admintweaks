<?php


namespace Restruct\BrillOOP\ORM;


class MySQLSchemaManagerNullable extends \SilverStripe\ORM\Connect\MySQLSchemaManager
{
    /**
     * Return a int type-formatted string
     *
     * @param array $values Contains a tokenised list of info about this data type
     * @return string
     */
    public function nullable_int($values)
    {
        //For reference, this is what typically gets passed to this function:
        //$parts=Array('datatype'=>'int', 'precision'=>11, 'null'=>'not null', 'default'=>(int)$this->default);
        //DB::requireField($this->tableName, $this->name, "int(11) not null default '{$this->defaultVal}'");
        return "int({$values['precision']}) " . $this->defaultClause($values);
    }
}
