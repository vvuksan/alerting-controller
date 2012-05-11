<?php

$base_dir = dirname(__FILE__);

# Load main config file.
require_once $base_dir . "/conf_default.php";

# Include user-defined overrides if they exist.
if( file_exists( $base_dir . "/conf.php" ) ) {
  include_once $base_dir . "/conf.php";
}

require_once $base_dir . "/nagios_commands.php";
require_once $base_dir . "/parse_nagios_status.php";

if ( isset($argv) ) {
   $arguments = commandline_arguments($argv);
} else {
   $arguments = $_REQUEST;
}

$nagios = parse_nagios_status_file($conf['nagios_status_file']);

print("<PRE>"); print_r($nagios);

?>