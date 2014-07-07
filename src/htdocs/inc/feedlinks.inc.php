<?php
	include_once 'terms.inc.php';

	function feedLink ($sizeUrl, $ageUrl, $format, $display, $basePath=null) {
		global $SUMMARY_PATH;
		if ($basePath === null) {
			$basePath = $SUMMARY_PATH;
		}

		return '<a href="' . $basePath . $sizeUrl . '_' . $ageUrl . '.' . $format .
				'">' . $display . '</a>';
	}
?>

<?php
	foreach ($dateRanges as $dateKey=>$dateRange) {
		print '
			<h3>' .
				$dateRange['name'] . '</h3>' .
				'<small>' . $dateRange['help'] . '</small>
			<ul>';

		foreach ($magRanges as $magKey=>$magRange) {
			print '
				<li>' .
					feedLink($magRange['url'], $dateRange['url'], $format,
							$magRange['name']) . '
				</li>
			';
		}

		print '</ul>';
	}
?>