<?php
	const TAB = '&nbsp;&nbsp;&nbsp;&nbsp;';
	include('Block.php');

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
		$blocks = readData();
		openAllOpened($blocks);
		for ($i = 0; $i < sizeof($blocks); $i++) {
			write($blocks[$i], strval($i).'/', $openclose);
			echo "<hr>";
		}

		echo '<form action="index.php?close=-1" method="post"> <input type="submit" value="Закрыть всё"></form>';
	}

	main();
?>