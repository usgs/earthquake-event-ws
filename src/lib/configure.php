<?php

  $CONFIG_FILE = '../conf/config.ini';

  if (!function_exists('configure')) {
    /**
     * Prompts user for a configuration $option and writes the response to the
     * configuration "ini" file.
     *
     * @param $option {String}
     *      The name of the option to configure.
     *
     * @param $default {String} Optional, default: <none>
     *      The default value to use if no answer is given.
     * @param $comment {String} Optional, default: $option
     *      Help text used when prompting the user. Also used as a comment in
     *      the configuration file.
     * @param $file {Resource} Optional, default: null
     *      The file to which the configuration is to be written. Null if do
     *      not write the configuration.
     * @param $secure {Boolean} Optional, default: false
     *      True if user input should not be echo'd back to the screen as it
     *      is entered. Useful for passwords.
     * @param $unknown {Boolean} Optional, default: false
     *      True if the configuration option is not a well-known option and
     *      a warning should be printed.
     *
     * @return {String}
     *      The configured value for the requested option.
     */
    function configure ($option, $default = '', $comment='', $file = false,
        $secure=false, $unknown=false) {
      global $NO_PROMPT;

      if ($NO_PROMPT) {
        return $default;
      }

      if ($unknown) {
        // Warn user about an unknown configuration option being used.
        print "\nThis next option ($option) is not a well-known " .
              "configuration option and\nmay not get used. You may still " .
            "want to define a value for this option since you\nare a " .
            "human and theoretically smarter than this program. This " .
            "may have happened\nif an old option has been deprecated " .
            "or removed.\n\n";
      }

      // Make sure we have good values for I/O.
      if ($default == null) { $default = '<none>'; }
      $help = ($comment !== null && $comment != '') ? $comment : $option;

      // Prompt for and read the configuration option value
      printf("%s [%s]: ", $help, $default);
      if ($secure) {system('stty -echo');}
      $value = trim(fgets(STDIN));
      if ($secure) {system('stty echo'); print "\n";}

      // Check the input
      if ($value == '' && $default != '<none>') { $value = $default; }

      // Write to the file if given
      if (is_resource($file)) {
        fwrite($file, sprintf("; %s\n%s = %s\n", $help, $option, $value));
      }

      // Always return the value
      return $value;
    }
  }

  // These are prompts used to help user when interactively configuring the
  // web application installation.
  $HELP_TEXT = array(
    // Mount paths for components
    'EVENT_PATH' => 'Absolute URL root path for event content',
    'FEED_PATH' => 'Absolute URL root path for feed content',
    'FDSN_PATH' => 'Absolute URL root path for fdsn event webservice',
    'MAPLIST_PATH' => 'Absolute URL for map/list search results',
    'PRODUCT_PATH' => 'Absolute URL root path for product content',

    // Indexer storage
    'storage_directory' => 'Product content storage directory',
    'storage_url' => 'Absolute URL root path for product storage',

    // Indexer database
    'db_hostname' => 'Product index database host name',
    'db_read_user' => 'Product index database user name',
    'db_read_pass' => 'Product index database password',
    'db_name' => 'Product index database schema name',

    // Feed configuration settings
    'SEARCH_PATH' => 'URL path to search page.',
    'MAX_SEARCH' => 'Maximum search results allowed (default = 20,000)',
    'API_VERSION' => 'Feed API version',
    'DEFAULT_MAXEVENTAGE' => 'Default maxEventAge parameter (seconds)',
    'INSTALLATION_TYPE' => '"actual" or "scenario" events.',

    'OFFSITE_HOST' => 'HTTP host for remote product index location'
  );

  // Defaults
  $DEFAULTS = array(
    // Mount paths for components
    'EVENT_PATH' => '/earthquakes/eventpage',
    'FEED_PATH' => '/earthquakes/feed',
    'FDSN_PATH' => '/fdsnws/event/1',
    'MAPLIST_PATH' => '/earthquakes/map',
    'PRODUCT_PATH' => '/ws/product',

    // Indexer storage
    'storage_directory' => '',
    'storage_url' => '/product',

    // Indexer database
    'db_hostname' => '',
    'db_read_user' => '',
    'db_read_pass' => '',
    'db_name' => '',

    // Feed settings
    'SEARCH_PATH' => '/earthquakes/search',
    'MAX_SEARCH' => '20000',
    'API_VERSION' => 'v1.0',
    'DEFAULT_MAXEVENTAGE' => '2592000',
    'INSTALLATION_TYPE' => 'actual',

    'OFFSITE_HOST' => ''
  );

  // allow environment override during container build
  // command line environment is added $_SERVER
  foreach ($DEFAULTS as $key=>$value) {
    if (isset($_SERVER[$key])) {
      $DEFAULTS[$key] = $_SERVER[$key];
    }
  }

  // Default action is to configure
  $configure_action = '3';

  // Check if previous configuration file exists
  if (file_exists($CONFIG_FILE)) {
    if ($NO_PROMPT) {
      $configure_action = 1;
    } else {
      $configure_action = '0';
    }
  }

  while (!($configure_action=='1'||$configure_action=='2'||$configure_action == '3')) {
    // File exists. Does user want to just go with previous configuration?
    print "Previous configuration file found. What would you like to do?\n";
    print "   [1] Use previous configuration.\n";
    print "   [2] Interactively re-configure using current configuration as defaults.\n";
    print "   [3] Interactively re-configure using default configuration as defaults.\n";
    print 'Enter the number corresponding to the action you would like to take: ';
    $configure_action = trim(fgets(STDIN));
    print "\n";
  }

  if ($configure_action == '1') {
    // Do not configure. File is in place and user wants to use it.
    print "Using previous configuration.\n";

    // pre-install depends on this variable
    $CONFIG = parse_ini_file($CONFIG_FILE);
  } else if ($configure_action == '2') {
    // Use current config as default and re-configure interactively.
    print "Using current config file as defaults, and interactively re-configuring.\n";
    $CONFIG = parse_ini_file($CONFIG_FILE);

    // Make sure all default parameters are in the new configuration.
    $CONFIG = array_merge($DEFAULTS, $CONFIG);

    // TODO :: Should this check be done in a loop where we notify the user
    // about each missing configuration parameter ???

    // TODO :: Should we do a reverse-check to make sure everything in the
    // config.ini file is also one of our defaults? This might be useful
    // during development if we accidently directly modify the config.ini
    // file to add a new parameter and it gets lost when we move into
    // production.
  } else if ($configure_action == '3') {
    // Use defaults and re-configure interactively.
    print "Reverting to default configuration and interactively re-configuring.\n";
    $CONFIG = $DEFAULTS;
  } else {
    print "Invalid answer. Please try again.\n";
  }

  // Write the configuration file
  if ($configure_action == '2' || $configure_action == '3') {
    $tmpfile = '/tmp/.feedapp-config.ini';
    $file = fopen($tmpfile, 'w');
    foreach ($CONFIG as $k=>$v) {
      $secure = (stripos($k, 'pass') !== false);
      $unknown = !isset($DEFAULTS[$k]);

      $CONFIG[$k] = configure(
        $k, // Name of option
        $v, // Default value
        (isset($HELP_TEXT[$k]))?$HELP_TEXT[$k]:null, // Help text
        $file, // File to write to
        $secure, // Should echo be turned off for inputs?
        $unknown // Is this a known/unkown option?
      );
    }
    system("mv $tmpfile $CONFIG_FILE");
  }

?>
