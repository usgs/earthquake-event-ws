<?php

/**
 * This script creates a single SQL script for installing the database
 * components for the web service.
 *
 * By default it includes "indexer", "feplus", and "fdsnws". Any of these can
 * be skipped by passing --without-<component>. While it may be time-efficient
 * to skip a given component, it is safe enough to run all components
 * regardless of existing data.
 *
 * Usage: php install_mysql.php [--without-<component>]
 *
 */

// default components
$components = array('indexer', 'feplus', 'fdsnws');
$skip_component = array();

// script to be msql_created
$output = 'install_mysql.sql';
if (file_exists($output)) {
	unlink($output);
}
$fp = fopen($output, 'a+');

// parse command line arguments
for ($i = 1; $i < count($argv); $i++) {
	if (strpos($argv, '--without-') === 0) {
		$skip_component[] = str_replace('--without-', '', $argv[$i]);
	}
}

chdir(dirname($argv[0]));

// create the script
foreach ($components as $component) {
	if (in_array($component, $skip_component)) {
		echo "Skipping component (per configuration): ${component}\n";
		continue;
	}

	$component_file = "${component}/_config.inc.php";

	if (!file_exists($component_file)) {
		echo "Skipping component (not configured): ${component}\n";
		continue;
	}

	// re-sets $phpfiles and $sqlfiles
	include $component_file;

	foreach ($files as $file) {
		$fullfile = "${component}/${file}";
		$extension = strtolower(substr($file, -4));

		if (!file_exists($fullfile)) {
			echo "Skipping file (not found): ${fullfile}\n";
			continue;
		}

		if ($extension === '.php') {
			ob_start();
			include $fullfile;
			fwrite($fp, ob_get_clean());
		} else if ($extension === '.sql') {
			fwrite($fp, file_get_contents($fullfile));
		} else {
			echo "Skipping file (extension unknown): ${fullfile}\n";
		}
	}

}

fclose($fp);