<?php


/**

 * arcTable is a PHP class for html table development.
 * This allows for concatenation of tables
 * and the creation of dynamically adjustable, javascript augmented tables.
 *
 * @license http://opensource.org/licenses/lgpl-3.0 GNU LGPL 3.0
 * @package Arcane
 * @subpackage fireUI
 * 
 * 
 * @author Alisdair Rankine <info@alisdairrankine.com>
 *
 *
 *
 */
class fireTable {


    /**
     *
     * @var array $tableArray 2D Array of cells
     */
    protected $tableArray;

    /**
     *
     * @var int number of columns in table.
     */
    protected $colCount;

    /**
     *
     * @var int number of rows in table.
     */
    protected $rowCount;
    
    /**
     * arcTable constructor. Optionally can be created witha  row-first 2D array
     *
     * @param mixed $tableData A string containing a well formed XML, a 2D (row first) array or a simpleXMLElement.
     *
     * @return fireTable This;
     */
     public function  __construct($tableData) {
        //If the table data is an array, make sure it is a 2D array, 1D arras should throw an exception
        if (is_array($tableData)){
            $newTable=array();
            foreach($tableData as $row){
                $newRow=array();
                if (is_array($row)){
                    foreach($row as $cell){
                        $newRow[]=strval($cell);
                    }
                    $newTable[]=$newRow;
                } else {
                    throw new InvalidArgumentException("Arrays must be 2D");
                }
            }
            $this->tableArray=$newTable;
        } else {
            // if the array is a simpleXML element
            if (is_a($tableData, "SimpleXMLElement")){
                $this->tableArray=$this->parseXML($tableData);

            } else {
                if (is_string($tableData)){
                    $xmlTable= new SimpleXMLElement($tableData);
                    $this->tableArray=$this->parseXML($xmlTable);
                } else {
                    throw new InvalidArgumentException("fireTables must be constructd from either a 2D array, a SimpleXMLElement object or a well formed xml table string");
                }
            }

        }
        $this->rectify();
        return $this;
    }

    /**
     * Populates the table froma  well formed xml table.
     *
     * @param SimpleXMLElement $xmlTable An XML table.
     *
     * @return array
     */
    protected function parseXML($xmlTable){
        
        if ($xmlTable->getName()!="table") throw new InvalidArgumentException ("xmlTable must be a table element");
        $newTable=array();
        foreach($xmlTable->children() as $rowElement){
            if ($xmlTable->getName()!="tr") throw new InvalidArgumentException ("xmlTable rows must be tr elements");
            $newRow=array();
            foreach($row->children() as $cell){
                if ($cell->getName()!="td") throw new InvalidArgumentException ("xmlTable cells must be td elements");
                $newCell=array();
                foreach($row->children() as $cellContents){$newCell[]=$cellContents->asXML();}
                $newRow[]=implode("", $newCell);
            }
            $newTable[]=$newRow;
        }
        return $newTable;
    }

    /**
     * This returns an HTML representation of the table.
     *
     * @return string
     */
    public function getHTML(){
        $HTML="<table>\n";
        foreach($this->tableArray as $row){
            $HTML .= "\t<tr>\n";
            foreach($row as $cell){
                $HTML.="\t\t<td>$cell</td>\n";
            }
            $HTML .="\t</tr>\n";

        }
        $HTML.="</table>\n";
        return $HTML;
    }

    /**
     * normalises cell count in rows
     */
    protected function rectify(){
        $rowCount1=0;
        $colCount1=0;
        foreach($this->tableArray as $row){
            $colCount1++;
            $rowCount = (count($row)>$rowCount1)?count($row):$rowCount1;
        }
        $this->rowCount=$rowCount1;
        $this->colCount=$colCount1;
        $newTable=array();
        foreach($this->tableArray as $row){
            $newRow=array();
            if (count($row)>$this->rowCount){
                $filler=array_fill(0, ($this->rowCount-count($row)), "");
                $newRow = array_merge($filler, $row);
            } else {
                $newRow = array_merge($filler, $row);
            }
            $newTable[]=$newRow;
        }
        $this->tableArray=$newTable;

    }


    /**
     *
     * @return array dimensions of the table
     */
    public function getDimensions(){
        $this->rectify();
        $return= array("rowCount"=>($this->rowCount),"colCount"=>($this->colCount));
        return $return;
    }

    /**
     *
     * @return array table represented as an array
     */
    public function getArray(){
        $this->rectify();
        return $this->tableArray;
    }

    /**
     *
     * @param fireTable $otherTable the table to join onto this
     */
    public function join($otherTable){
        if (!is_a($otherTable, "fireTable")) throw new InvalidArgumentException ("\$otherTable must be a fireTable object");
        $otherDimensions=$otherTable->getDimensions();
        $otherArray=$otherTable->getArray();
        $maxCols=($otherDimensions["colCount"]>($this->colCount))?$otherDimensions["colCount"]:($this->colCount);
        $thisArray=$this->tableArray;
        $newTable=array();

        for ($i=0;$i<$maxCols;$i++){
            $newRow=array();
            if (!isset($thisArray[$i]))$thisArray[$i]=array_fill(0, $this->rowCount, "");
            if (!isset($otherArray[$i]))$otherArray[$i]=array_fill(0, $otherDimensions["rowCount"], "");
            $newRow=array_merge($thisArray[$i],$otherArray[$i]);
            $newTable[]=$newRow;
        }
        $this->tableArray=$newTable;
        $this->rectify();
    }

    /**
     *
     * @param mixed $table1 A string containing a well formed XML, a 2D (row first) array or a simpleXMLElement.
     * @param mixed $table2 A string containing a well formed XML, a 2D (row first) array or a simpleXMLElement.
     * @return fireTable the union of both the tables
     */
    public static function union($table1,$table2){
        $t1=new fireTable($table1);
        $t2=new fireTable($table2);
        $t1->join($t2);
        return $t1;
    }

}
?>
