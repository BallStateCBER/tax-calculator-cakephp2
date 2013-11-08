<?php
	// Adapted from brownfields website

	function outputCsvLine(&$vals, $key, $filehandler) {
		fputcsv($filehandler, $vals, ',', '"');
	}
	function outputCsv($data) {
		$outstream = fopen("php://output", 'w');
		array_walk($data, 'outputCsvLine', $outstream);
		fclose($outstream);
	}

	if (isset($_GET['debug'])) {
		echo '<pre>';
	}

	outputCsv($rows);

	if (isset($_GET['debug'])) {
		echo '</pre>';
	}
?>