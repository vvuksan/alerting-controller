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

if ( (! isset($_REQUEST['host_name']) or ! in_array($_REQUEST['host_name'], $hosts) ) and !isset($_REQUEST['regex_search']) ) {
  print '
      <div class="alert alert-error">
        <strong>Problem!</strong> Host name is not valid ' . $_REQUEST['host_name'] . '. Please fix and resubmit.
      </div>
  ';
  exit(1);
} else {
  $host_name = $_REQUEST['host_name'];
}

if ( isset($_REQUEST['regex_search']) and $_REQUEST['regex_search'] == 1 ) {
  $regex_search = TRUE;
} else {
  $regex_search = FALSE;
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

######################################################################################
# If alert hash is not available we are operating on a single host
######################################################################################
if ( !isset($_REQUEST['alert']) or size($_REQUEST['alert']) == 0 ) {

  switch ( $_REQUEST['action'] ) {

      case "disable_notifications":
	disable_host_notifications($host_name);
	break;
      case "enable_notifications":
	enable_host_notifications($host_name);
	break;
      case "downtime":
	schedule_host_downtime($host_name, is_numeric($_REQUEST['downtime_duration']) ? $_REQUEST['downtime_duration'] : 0 );
	foreach ($nagios['services'][$host_name] as $alert_name => $details ) {
	  print "Disabling $alert_name<br>";
	  schedule_service_downtime($host_name, $alert_name, is_numeric($_REQUEST['downtime_duration']) ? $_REQUEST['downtime_duration'] : 0 );
	}
	break;

  }

  print "Command sent";

} else {

  foreach ( $_REQUEST['alert'] as $index => $alert ) {
    // If it's regex_search we are encoding hostname and alert with a pipe
    if ( $regex_search ) {
      if ( preg_match("/^(.*)(\|)(.*)/", $alert, $out ) ) {
	$host_name = $out[1];
	$alert_name = $out[3];
      } else {
	continue;
      }
    } else {
      $alert_name = $alert;
    }

    // Make sure alert exists
    if ( isset($nagios['services'][$host_name][$alert_name] ) ) {

      print "<strong>" . $alert .  "</strong> " . $action . "<br />";

      switch ( $_REQUEST['action'] ) {

	  case "disable_notifications":
	    disable_service_notifications($host_name, $alert_name);
	    break;
	  case "enable_notifications":
	    enable_service_notifications($host_name, $alert_name);
	    break;
	  case "downtime":
	    schedule_service_downtime($host_name, $alert_name, is_numeric($_REQUEST['downtime_duration']) ? $_REQUEST['downtime_duration'] : 0 );
	    break;

      }

    }

  } // end of foreach ( $_REQUEST['alert'] as $index => $alert ) {

} // end if ( !isset($_REQUEST['alert']) or size($_REQUEST['alert']) == 0 ) {


print '</div>';


?>