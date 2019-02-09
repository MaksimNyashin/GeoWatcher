<?php
const TAB = '&nbsp;&nbsp;&nbsp;&nbsp;';
/**
* 
*/
class Block {
	public $name;
	public $array = array();
	public $bool;
	public $level = 0;
	public $enable = 0;
	function getString($str){
		#echo $str, "<br>";
		#echo str_replace(array("-", ":"), "", $str),  "<br>";
		return str_replace(":", "", str_replace("-", "", $str));
	}
	function __construct($argument, $level)
	{
		$divider = "/\n\-";
		$this->level = $level;
		for ($i = 0; $i < $level; $i++)
			$divider .= "\-";
		$divider .= "\ /";
		$spl = preg_split(strval($divider), $argument, PHP_INT_MAX);
		if (sizeof($spl) > 1) {
			$this->bool = 1;
			//$this->name = $spl[0];
			$this->name = $this->getString($spl[0]);
			for ($i = 1; $i < sizeof($spl); $i++)
				$this->array[$i - 1] = new Block($spl[$i], $level + 1);
		} else {
			$spl = explode(":", $argument, 2);
			//$this->name = $spl[0];
			$this->name = $this->getString($spl[0]);
			$spl2 = explode(";", str_replace("\n", "", $spl[1]), PHP_INT_MAX);
			$cnt = 0;
			for ($i = 0; $i < sizeof($spl2); $i++) {

				if (strlen($spl2[$i]) > 0) {
					$this->array[$cnt] = $spl2[$i];
					$cnt++;
				}
			}
			if (sizeof($this->array) > 0)
				$this->bool = 1;
		}
	}
	public function OnClick() {
		$this->enable ^= 1;
	}
}

function write($block, $code, $openclose) {
	$opcl = "open";
	if ($block->enable)
		$opcl = "close";
	$show = "collapse; display:none";
	if ($block->enable == 1)
		$show = "visible";
	for ($i = 0; $i < $block->level; $i++)
		echo TAB;
	//<div aria-expanded="$show">
	echo <<<END2
	<a href="index.php?$opcl=$code">$block->name</a>
	<br>
	<div style="visibility: $show">
	<!--<form action="index.php?$opcl=$code" method="post">
	<input type="submit" value = "$block->name">-->
	</form>
END2;
	/*if ($block->enable == 0) {
		echo "</div>";
		return;
	}*/
	for ($i = 0; $i < sizeof($block->array); $i++) {
		if ($block->array[$i]->bool == 0) {
			//echo substr($block->array[$i]->name, -1), $block->array[$i]->name, "<br>";
			for ($j = 0; $j <= $block->level; $j++)
				echo TAB;
			if ($block->array[$i] instanceof Block)
				echo $block->array[$i]->name, "<br>";
			else
				echo $block->array[$i], "<br>";
		}
		else
			write($block->array[$i], $code.strval($i).'/', $openclose);
	}
	echo "</div>";
}

function getOpened() {
	$open = $_GET['open'];
	$opened = file_get_contents("opened.txt");
	if ($open != NULL)
		$opened .= "#".$open;
	$close = $_GET['close'];
	if ($close != NULL) {
		$splited = explode("#", $opened, PHP_INT_MAX);
		$newOpened = "";
		if ($close != -1) {
			for ($i = 0; $i < sizeof($splited); $i++) {
				if (strpos($splited[$i], $close) === 0 || strlen($splited[$i]) == 0)
					continue;
				$newOpened .= "#".$splited[$i];
			}
		}
		$opened = $newOpened;
	}
	file_put_contents("opened.txt", $opened);
	return $opened;
}

function openBlock($block, $string) {
	$strexp = explode("/", $string, 2);
	if (sizeof($strexp) == 1) {
		$block->onClick();
		return;
	}
	if (sizeof($block->array) <= intval($strexp[0]) || $block->bool == 0)
		return;
	openBlock($block->array[strval($strexp[0])], $strexp[1]);
}

function openAllOpened($blocks) {
	$opened = explode("#", getOpened(), PHP_INT_MAX);
	for ($i = 1; $i < sizeof($opened); $i++)
	{
		$strexp = explode("/", $opened[$i], 2);
		openBlock($blocks[intval($strexp[0])], $strexp[1]);
	}
}

function main() {
	$basic = file_get_contents("Information.txt");
	$arr = explode("\n\n", $basic);
	$blocks = array();
	for ($i = 0; $i < sizeof($arr); $i++) {
		$blocks[$i] = new Block($arr[$i], 0);
	}
	openAllOpened($blocks);
	for ($i = 0; $i < sizeof($arr); $i++) {
		write($blocks[$i], strval($i).'/', $openclose);
		echo "<hr>";
	}

	echo '<form action="index.php?close=-1" method="post"> <input type="submit" value="Закрыть всё"></form>';
}

main();
?>