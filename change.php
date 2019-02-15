<?php 
	include("Block.php");

	function combineToWrite($block, $prefix, $isAllText) {
		$ret = "";
		//echo $block->name, "<br>";
		if ($isAllText == 0) {
			if (strlen($prefix) > 0)
				$ret .= $prefix." ";
			$ret .= $block->name.":\n";
			if ($block instanceof Block) {
				$bool = 1;
				for ($i = 0; $i < sizeof($block->array); $i++)
					if ($block->array[$i] instanceof Block)
						$bool = 0;
				for ($i = 0; $i < sizeof($block->array); $i++){
					$ret .= combineToWrite($block->array[$i], $prefix."-", $bool);
				}
				if (substr($ret, -1) != "\n")
					$ret .= "\n";
			}
		} else {
			if ($block instanceof Block)
				$ret = $block->name.";";
			else
				$ret = $block.";";
		}
		return $ret;
	}

	function main() {
		$blocks = readData();
		$ans = "";
		for ($i = 0; $i < sizeof($blocks); $i++)
			$ans .= combineToWrite($blocks[$i], "", 0)."\n";
		file_put_contents("information2.txt", $ans);
	}

	main();
?>