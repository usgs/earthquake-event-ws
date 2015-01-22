<?php


/**
 * Content represents a group of bytes, with mime type and modification date.
 *
 * This class makes several simplifying assumptions:
 *   - all content is file based
 *   - all content is url addressable
 */
class Content {

	/** The content mime type. */
	private $contentType;
	/** When the content was last modified. */
	private $lastModified;
	/** The content length. */
	private $length;

	/** For inline content that has no path. */
	private $bytes;
	/** File content. */
	private $file;
	/** URL to file content. */
	private $url;


	public function __construct($contentType, $lastModified, $length, $file = null, $url = null) {
		$this->contentType = $contentType;
		$this->lastModified = $lastModified;
		$this->length = $length;
		$this->file = $file;
		$this->url = $url;
	}

	public function getContentType() { return $this->contentType; }
	public function setContentType($type) { $this->contentType = $type; }

	public function getLastModified() { return $this->lastModified; }
	public function setLastModified($modified) { $this->lastModified = $modified; }

	public function getLength() { return $this->length; }
	public function setLength($length) { $this->length = $length; }

	public function getFile() { return $this->file; }
	public function setFile($file) { $this->file = $file; }

	public function getURL() { return $this->url; }
	public function setURL($url) { $this->url = $url; }

	public function setBytes($bytes) { $this->bytes = $bytes; }
	public function getContent() {
		if (isset($this->bytes)) {
			return $this->bytes;
		} else if (file_exists($this->file)) {
			return file_get_contents($this->file);
		} else if ($this->url !== null) {
			return file_get_contents($this->url);
		} else {
			return null;
		}
	}

	public function toArray() {
		$r = array(
			'contentType' => $this->getContentType(),
			'lastModified' => $this->getLastModified(),
			'length' => safeintval($this->getLength())
		);

		$url = $this->getURL();
		if ($url != null) {
			$r['url'] = $url;
		} else {
			$r['bytes'] = $this->getContent();
		}

		return $r;
	}

}
