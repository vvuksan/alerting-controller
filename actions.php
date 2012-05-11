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

$nagios = parse_nagios_status_file($conf['nagios_status_file']);

$hosts = array_keys($nagios['hosts']);

if ( ! isset($_REQUEST['host_name']) || ! in_array($_REQUEST['host_name'], $hosts) ) {
  print '
      <div class="alert alert-error">
        <strong>Problem!</strong> Host name is not valid ' . $_REQUEST['host_name'] . '. Please fix and resubmit.
      </div>
  ';
}

switch ( $_REQUEST['action'] ) {

    case "disable":
      $action = "disabled";
      break;
    case "enable":
      $action = "enabled";
      break;
    case "downtime":
      $action = "scheduled for downtime";
      break;

}

print '
<div class="alert alert-success">
';

foreach ( $_REQUEST['alert'] as $index => $alert ) {
  print "<strong>" . $alert .  "</strong> is being " . $action . "<br />";
}

print '</div>';


?>