<?php


/**
 * ProductStorage stores products.
 *
 * This implementation is simplified from that of PDL:
 *  - read only
 *  - file backed
 *  - essentially a URLProductStorage.
 */
class ProductStorage {

  public static $PRODUCT_XML_FILENAME = "product.xml";

  /** The filesystem path to storage directory. */
  private $baseDirectory;

  /** The url path to storage directory. */
  private $baseURL;


  public function __construct($baseDirectory, $baseURL) {
    $this->baseDirectory = $baseDirectory;
    $this->baseURL = $baseURL;
  }


  public function hasProduct($productid) {
    $directory = $this->getProductFile($productid);
    $xml = $directory . '/' . self::$PRODUCT_XML_FILENAME;

    if (is_dir($directory) && file_exists($xml)) {
      return true;
    }

    return false;
  }

  public function getProduct($productid) {
    $directory = $this->getProductFile($productid);
    $xml = $directory . '/' . self::$PRODUCT_XML_FILENAME;
    $url = $this->getProductURL($productid) . '/' . self::$PRODUCT_XML_FILENAME;

    //parse xml
    try {
      if (is_file($xml)) { // Prefer local files
        $parsed = simplexml_load_file($xml);
      } else { // Fall back to remote files if needed
        $opts = array(
          'http'=> array(
            'method'=> 'GET',
            'header'=>'Accept-encoding: identity'
          )
        );
        $context = stream_context_create($opts);
        $contents = file_get_contents($url, false, $context);
        $parsed = simplexml_load_string($contents);
      }
      if ($parsed == null) {
        return null;
      }
    } catch (Exception $e) {
      return null;
    }

    $product = new Product(ProductId::parse(strval($parsed['id'])));
    $product->setStatus(strval($parsed['status']));
    $product->setTrackerURL(strval($parsed['trackerURL']));
    $product->setSignature(strval($parsed->signature));

    $props = array();
    foreach ($parsed->property as $prop) {
      $props[strval($prop['name'])] = strval($prop['value']);
    }
    $product->setProperties($props);

    $links = array();
    foreach ($parsed->link as $link) {
      $links[strval($link['rel'])][] = strval($link['href']);
    }
    $product->setLinks($links);


    // THIS IS THE NEW STYLE OF LOADING CONTENTS
    $contents = array();
    $foundURLContent = false;
    $productBaseURL = $this->getProductURL($product->getId());
    foreach ($parsed->content as $content) {
      // convert to milliseconds
      $content_time = strtotime(strval($content['modified']));
      if ($content_time !== 0) {
        $content_time .= '000';
      }
      $c = new Content(strval($content['type']), $content_time, strval($content['length']));

      // all contents (except inline) have href which is a file: url
      if (isset($content['href'])) {
        $file = str_replace("file:", "", strval($content['href']));
        $c->setFile($file);
        $c->setURL($productBaseURL . '/' . $content['path']);
        $foundURLContent = true;
      } else if ($content['encoded']) {
        $c->setBytes(base64_decode(strval($content)));
      } else {
        $c->setBytes(strval($content));
      }

      $contents[strval($content['path'])] = $c;

    }

    if (!$foundURLContent) {
      // this may mean it's an old product before storage persisted urls
      // try to load it the old fashioned way (which was replaced for a reason)
      // THIS IS THE OLD STYLE OF LOADING CONTENTS...
      $contents = $this->getContents($directory);

      //remove the xml file from contents list
      unset($contents[self::$PRODUCT_XML_FILENAME]);

      //add any inline content
      foreach ($parsed->content as $content) {
        // convert to milliseconds
        $content_time = strtotime(strval($content['modified']));
        if ($content_time !== 0) {
          $content_time .= '000';
        }
        $c = new Content(strval($content['type']), $content_time, strval($content['length']));

        if ($content['encoded']) {
          $c->setBytes(base64_decode(strval($content)));
        } else {
          $c->setBytes($content);
        }

        $contents[strval($content['path'])] = $c;
      }
    }

    $product->setContents($contents);
    return $product;
  }

  /**
   * Get a content object for every file in directory.
   */
  protected function getContents($directory, $basedir=null) {
    $contents = array();
    if ($basedir == null) {
      $basedir = $directory . '/';
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    foreach (glob($directory . '/**') as $file) {
      if (is_dir($file)) {
        //recurse into sub directories
        $dir_contents = $this->getContents($file, $basedir);
        foreach ($dir_contents as $path => $content) {
          $contents[$path] = $content;
        }
      } else {
        $c = new Content(finfo_file($finfo, $file), filemtime($file), filesize($file));
        $c->setFile($file);
        $c->setURL(str_replace($this->baseDirectory, $this->baseURL, $file));
        $contents[str_replace($basedir, "", $file)] = $c;
      }
    }
    finfo_close($finfo);

    return $contents;
  }

  protected function getProductPath($productid) {
    $path = '';

    if (!$productid) {
      return '';
    }

    $path .= $productid->getType();
    $path .= '/';
    $path .= $productid->getCode();
    $path .= '/';
    $path .= $productid->getSource();
    $path .= '/';
    $path .= $productid->getUpdateTime();

    return $path;
  }

  public function getProductFile($productid) {
    return $this->baseDirectory . '/' . $this->getProductPath($productid);
  }

  protected function getProductURL($productid) {
    return $this->baseURL . '/' . $this->getProductPath($productid);
  }

}
