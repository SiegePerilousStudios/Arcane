<?php
/**
 * Takes in 2 well formed xHtml tables and concatenates them horizontally
 * @license http://opensource.org/licenses/lgpl-3.0 GNU LGPL 3.0
 * @author Alisdair Rankine <info@alisdairrankine.com>
 * @param string $table1 first table (well formed xHTML)
 * @param string $table2 second table (well formed xHTML)
 * @param boolean $leftNormalise fill empty columns to the left or right (defaults to true)
 * @param string $emptyRow xHTML to fill empty columns (well formed xHTML)
 */
function concatenateTables($table1,$table2,$leftNormalise=true,$emptyRow="<td></td>"){
$eR=$emptyRow;$lN=$leftNormalise;$t1= new SimpleXMLElement($table1);$t2= new SimpleXMLElement($table2);$tA1=array();$tA2=array();$tA=array();$cC1=0;$cC2=0;
foreach($t1->children() as $row){
$cC1=($row->count()>$cC1)?$row->count():$cC1;
$rA1=array();
foreach ($row->children() as $col){$rA1[]=$col->asXML();}
$tA1[]=$rA1;}	
foreach($t2->children() as $row){
$cC2=($row->count()>$cC2)?$row->count():$cC2;
$rA2=array();
foreach ($row->children() as $col){$rA2[]=$col->asXML();}
$tA2[]=$rA2;}
$mR = ($t1->count()>$t2->count())?$t1->count():$t2->count();
for ($i=0;$i<$mR;$i++){
$tA1[$i]= (!isset($tA1[$i]))?array_fill(0,$cC1,""):$tA1[$i];
$tA2[$i]= (!isset($tA2[$i]))?array_fill(0,$cC2,""):$tA2[$i];
$nnL1 = ($lN)?($cC1-count($tA1[$i])):0;
$nL1=($nnL1>0)?array_fill(0,$nnL1,$eR):array();
$nnR1 = (!$lN)?($cC1-count($tA1[$i])):0;
$nR1=($nnR1>0)?array_fill(0,$nnR1,$eR):array();
$nnL2 = ($lN)?($cC2-count($tA2[$i])):0;
$nL2=($nnL2>0)?array_fill(0,$nnL2,$eR):array();
$nnR2 = (!$lN)?($cC2-count($tA2[$i])):0;
$nR2=($nnR2>0)?array_fill(0,$nnR2,$eR):array();
$tA[$i] = array_merge($nL1,$tA1[$i],$nR1,$nL2,$tA2[$i],$nR2);}
$tH="<table>\n";foreach ($tA as $tARow){$tH.="\t<tr>\n";foreach ($tARow as $tAC){$tH.="\t\t".$tAC."\n";}$tH.="\t</tr>\n";}$tH.="</table>";
return $tH;
}?>