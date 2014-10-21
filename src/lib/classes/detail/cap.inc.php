<?php


if( $event == null ) {
	header('HTTP/1.0 404 Not Found');
	echo 'Event not found';
	return;
} else if ($event->isDeleted()) {
	header('HTTP/1.0 404 Not Found');
	echo 'Event deleted';
	return;
}


$content = null;

// try to find cap product
$cap = $event->getPreferredProduct('cap');
if ($cap !== null) {
	$product = $storage->getProduct($cap->getId());
	if ($product !== null) {
		$contents = $product->getContents();
		if (isset($contents["capalert.xml"])) {
			$content = $contents["capalert.xml"];
		}
	}
}


if ($content === null) {
	header('HTTP/1.0 404 Not Found');
	echo 'No CAP alert for this event';
	return;
}


header('Content-type: application/cap+xml');
echo $content->getContent();
