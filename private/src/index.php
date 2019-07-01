<?php
/**
 * Created by PhpStorm.
 * User: danielhemmerich
 * Date: 25.07.18
 * Time: 20:34
 */

try {
	require_once __DIR__ . '/setup.php';
	require_once __DIR__ . '/../vendor/autoload.php';
	require_once __DIR__ . '/XSD.php';
	require_once __DIR__ . '/View.php';

	$title = 'NURVIS online documentation';
	$versions = [];

	$dir = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator(
		__DIR__ . "/../xsd/",
		FilesystemIterator::SKIP_DOTS
	));

	foreach ($dir as $fileinfo) {
		$matches = [];
		preg_match(
			"/([0-9-]+)\s([0-9]+)_([A-Z]+)_([a-zA-Z-]+)_(version_dv.([0-9.]+))_([request|response]+).xsd/mi",
			$fileinfo->getFilename(),
			$matches
		);

		$date = $matches[1];
		$time = $matches[2];
		$dialect = $matches[3];
		$method_name = $matches[4];
		$version_name = $matches[5];
		$version_number = $matches[6];
		$request_or_response = $matches[7];

		if(!isset($versions[$version_number][$dialect])) {
			$versions[$version_number][$dialect] = [
				'methods'	=>	[]
			];
		}

		$xsdstring = file_get_contents(
			$fileinfo->getPathname()
		);

		$xsd = new XSD(
			$xsdstring
		);
		if(!isset($versions[$version_number][$dialect]['methods'][$method_name])) {
			$versions[$version_number][$dialect]['methods'][$method_name] = [];
		}
		$versions[$version_number][$dialect]['methods'][$method_name][$request_or_response] = $xsd;
	}
	ksort($versions);

	$view = new View(
		$title,
		$versions,
		$_GET['version'] ?? $version_number,
		$_GET['dialect'] ?? $dialect
	);

	if(isset($_POST['export'])) {
		$mpdf = new \Mpdf\Mpdf();
		$mpdf->WriteHTML($view->getPdf());
		$mpdf->Output();
	} else {
		echo $view;
	}
} catch(Exception $exception) {
	echo $exception;
}