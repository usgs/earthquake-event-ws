<?php


class Formatter {


	/**
	 * Format a magnitude.
	 *
	 * @param $magnitude magnitude to format, or null for ?.
	 * @return formatted magnitude.
	 */
	public function formatMagnitude($magnitude) {
		if ($magnitude === '' || $magnitude === null) {
			return 'M ?';
		} else {
			return 'M ' . number_format($magnitude, 1);
		}
	}

	/**
	 * Format a depth.
	 *
	 * @param $depth depth to format.
	 * @return formatted depth.
	 */
	public function formatDepth($depth) {
		if ($depth === '' || $depth === null) {
			return '?';
		} else {
			return number_format($depth, 2) . ' km' .
				' (' . number_format($depth*0.621371192, 2) . ' mi)';
		}
	}

	/**
	 * Format a latitude.
	 *
	 * @param $latitude latitude to format.
	 * @return formatted latitude.
	 */
	public function formatLatitude($latitude) {
		return number_format(abs($latitude), 3) .
				'&deg;' . ($latitude < 0 ? 'S' : 'N');
	}

	/**
	 * Format a longitude.
	 *
	 * @param $longitude longitude to format.
	 * @return formatted longitude.
	 */
	public function formatLongitude($longitude) {
		return number_format(abs($longitude), 3) . 
				'&deg;' . ($longitude < 0 ? 'W' : 'E');
	}

	/**
	 * Format a datetime.
	 *
	 * @param $time millisecond epoch timestamp
	 * @param $offset (Default null) offset from UTC in minutes.
	 */
	public function formatDate($time, $offset=null) {
		$time = intval(substr($time, 0, -3));
		$tz = 'UTC';
		if ($offset != null) {
			$time = $time + (60*intval($offset));
			$tz = $this->formatOffset($offset);
		}
		return gmdate('Y-m-d H:i:s', $time) . ' ' . $tz;
	}

	/**
	 * Format an offset, ISO8601 style.
	 *
	 * @param $offset offset from UTC in minutes.
	 */
	public function formatOffset($offset) {
		$offset = intval($offset);
		$sign = ($offset >= 0 ? "+" : "-");

		$offset = abs($offset);
		$hours = intval($offset / 60);
		$minutes = intval($offset % 60);

		return $sign . sprintf("%02d:%02d", $hours, $minutes);
	}

	
	/**
	 * Format a date using iso8601.
	 *
	 * @param $time millisecond epoch timestamp.
	 * @param $offset (Default null offset from UTC in minutes.
	 */
	public function formatDateIso($time, $offset=null) {
		$tz = 'Z';
		$seconds = intval(substr($time, 0, -3));
		$milliseconds = substr($time, -3);
		if ($offset != null) {
			$seconds = $seconds + (60*intval($offset));
			$tz = $this->offset($offset);
		}
		return gmdate('Y-m-d\TH:i:s', $seconds) . '.' . $milliseconds . $tz;
	}

}
