<?php
	$cNode = $_POST['cNode'];
	$pNode = $_POST['pNode'];
		
	$mat[$cNode][$pNode] = true;
	$mat[$pNode][$cNode] = true;

	return $mat;
?>