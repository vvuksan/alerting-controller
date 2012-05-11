<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<link type="text/css" href="css/smoothness/jquery-ui-1.8.14.custom.min.css" rel="stylesheet" />
<link type="text/css" href="css/bootstrap.css" rel="stylesheet" />
<link type="text/css" href="css/bootstrap-responsive.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.14.custom.min.js"></script>
<script type="text/javascript" src="js/combobox.js"></script>
<style>
.state_up, .state_ok { background-color: #89F691; }
.state_warning { background-color: #FDFB8C; }
.state_critical, .state_down { background-color: #F39C9B; }
.state_pending { background-color: #488acf; }
.state_unknown { background-color: orange; }
</style>
<script>
$(function() {
      $("#host_chooser").combobox();
      $(".submit_button").button();
    });

function checkAll(status) {
  $(".checkbox").each( function() {
    $(this).attr("checked",status);
  })
}

function sendAction() {
  $("#results").html('<img src="img/spin.gif">');
  $.get('actions.php', $("#alert-form").serialize() , function(data) {
    $("#results").html(data);
  });
  return false;
}

</script>
</head>
<body>

<div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="#">Alert Controller</a>
          <div class="nav-collapse">
            <ul class="nav">
              <li class="active"><a href="#">Home</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container">
    <div class="hero-unit">
        <h1>Alert Controller</h1>
    </div>
  <div class="row">
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


$states = array ( 
  0 => "ok",
  1 => "warning",
  2 => "critical",
  3 => "unknown"
);

?>
<div class="span6">
  <form id="alert-form"><select id="host_chooser" onChange="form.submit();" name=host_name>
  <option value="none">Select one</option>";
<?php
  foreach ( $nagios['hosts'] as $host => $alerts ) {
    print "<option value=\"" . $host . "\">" . $host . "</option>";
  }
  print "</select>";

if ( isset($arguments['host_name']) && $arguments['host_name'] != "none" ) {

  $host_name = $arguments['host_name'];

  print "<table border=1>
  <tr><th>&nbsp;</th><th>Alert</th></tr>";
  foreach ( $nagios['services'][$host_name] as $alert => $alert_settings ) {
    $current_state = $alert_settings['current_state'];
    print "<tr class=\"state_" . $states[$current_state] . "\"><td><input type=\"checkbox\" class=\"checkbox\" name=\"alert[]\" value=\"" . $alert . "\"></td>";
    print "<td>" . $alert . "</td></tr>";
  }
  print "<tr><td colspan=2 align=\"center\"><button class=\"btn btn-inverse\" id=\"select_all\" onClick=\"checkAll(this.form); return false;\">Select All</button></tr>";
  print "<tr><th>Disable</th><td><input type=\"radio\" name=\"action\" value=\"disable_notifications\" checked>Disable Notifications</input></td></tr>";
  print "<tr><th>Enable</th><td><input type=\"radio\" name=\"action\" value=\"enable_notifications\">Enable Notifications</input></td></tr>";
  print "<tr><th>Schedule Downtime</th><td><input type=\"radio\" name=\"action\" value=\"downtime\">Downtime</input>, 
   Duration <input size=\"4\" type=\"text\" value=\"1200\" name=\"downtime_duration\"> seconds</td></tr>";
  print "</table>";


  print "<p> </p><input class=\"submit_button btn btn-inverse\" type=\"submit\" onclick=\"sendAction(); return false;\" value=\"Submit\" />
  </form>
  <script>
  $(\"#host_chooser\").val(\"" . $host_name . "\");
  </script>
  ";

}

#print "<PRE>";print_r($nagios);

?>
</div> <!-- span8 -->
<div id="results" class="span6"></div>
</div> <!-- /row -->
</div> <!-- /container -->

</body>
</html>