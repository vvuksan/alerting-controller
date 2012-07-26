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

if ( !isset($_REQUEST) )
  die("Search term not supplied");

$search_term = $_REQUEST['search_term'];

$nagios = parse_nagios_status_file($conf['nagios_status_file']);

$count = 0;

$html = "";

foreach ( $nagios['services'] as $host_name => $alerts ) {
  foreach ( $alerts as $alert => $alert_settings ) {
    // Matching search 
    if ( preg_match("/" . $search_term . "/i", $alert ) ) {
      if ( isset($alert_settings['notifications_enabled']) && $alert_settings['notifications_enabled'] == 0 ) {
	$state = "notification";
      } else {
	$current_state = $alert_settings['current_state'];
	$state = $states[$current_state];
      }

      $html .= "<tr class=\"state_" . $state . "\"><td><input type=\"checkbox\" class=\"checkbox\" name=\"alert[]\" value=\"" . $host_name . "|" . $alert . "\"></td>";
      $html .= "<td>" . $host_name . "</td><td>" . $alert . "</td></tr>";

      $count++;

    } // end of if ( preg_match("/" . $search_term . "/i"

  }
}

if ( $count > 0 ) {

  print "<form id=\"alert-form\">
  <input type=\"hidden\" name=\"regex_search\" value=1>
  <table border=1><tr><th>&nbsp;</th><th>Hostname</th><th>Alert</th></tr>";

  print $html;

  print "<tr><td colspan=3 align=\"center\"><button class=\"btn btn-inverse\" id=\"select_all\" onClick=\"checkAll(this.form); return false;\">Select All</button></tr>";
  print "<tr><th colspan=2>Disable</th><td><input type=\"radio\" name=\"action\" value=\"disable_notifications\" checked>Disable Notifications</input></td></tr>";
  print "<tr><th colspan=2>Enable</th><td><input type=\"radio\" name=\"action\" value=\"enable_notifications\">Enable Notifications</input></td></tr>";
  print "<tr><th colspan=2>Schedule Downtime</th><td><input type=\"radio\" name=\"action\" value=\"downtime\">Downtime</input>, 
  Duration <input size=\"4\" type=\"text\" value=\"1200\" name=\"downtime_duration\"> seconds</td></tr>";
  print "</table>";


  print "<p> </p><input class=\"submit_button btn btn-inverse\" type=\"submit\" onclick=\"sendAction(); return false;\" value=\"Submit\" />
  </form>
  <script>
  $(\"#host_chooser\").val(\"" . $host_name . "\");
  </script>
  ";

} else {

?>
    <div class="alert">
    No results.
    </div>
<?php

}

?>