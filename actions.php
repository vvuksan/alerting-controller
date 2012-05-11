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

    case "disable_notifications":
      $action = "notifications will be disabled";
      break;
    case "enable_notifications":
      $action = "notifications will be enabled";
      break;
    case "downtime":
      $action = "scheduled for downtime";
      break;

}

print '
<div class="alert alert-success">
';

foreach ( $_REQUEST['alert'] as $index => $alert ) {
  print "<strong>" . $alert .  "</strong> " . $action . "<br />";

  switch ( $_REQUEST['action'] ) {

      case "disable_notifications":
	disable_service_notifications($_REQUEST['host_name'], $alert);
	break;
      case "enable_notifications":
	enable_service_notifications($_REQUEST['host_name'], $alert);
	break;
      case "downtime":
	schedule_service_downtime($_REQUEST['host_name'], $alert, is_numeric($_REQUEST['downtime_duration']) ? $_REQUEST['downtime_duration'] : 0 );
	break;

  }

}

print '</div>';


?>