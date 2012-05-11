<?php


##############################################################################
# Copied from
# http://www.php.net/features.commandline
#If the argument is of the form –NAME=VALUE it will be represented in the array as an element 
#with the key NAME and the value VALUE. I the argument is a  flag of the form -NAME it will be 
#represented as a boolean with the name NAME with a value of true in the associative array.
#Example:
#
#<?php print_r(arguments($argv));
# php5 myscript.php --user=nobody --password=secret -p
#
#Array
#(
#    [user] => nobody
#    [password] => secret
#    [p] => true
#)
function commandline_arguments($argv) {
    $_ARG = array();
    foreach ($argv as $arg) {
        if (ereg('--[a-zA-Z0-9]*=.*',$arg)) {
            $str = split("=",$arg); $arg = '';
            $key = ereg_replace("--",'',$str[0]);
            for ( $i = 1; $i < count($str); $i++ ) {
                $arg .= $str[$i];
            }
                        $_ARG[$key] = $arg;
        } elseif(ereg('-[a-zA-Z0-9]',$arg)) {
            $arg = ereg_replace("-",'',$arg);
            $_ARG[$arg] = 'true';
        }
   
    }
return $_ARG;
}


///////////////////////////////////////////////////////////////////////////////
// Function that opens up a pipe to Nagios command file and sends a command
///////////////////////////////////////////////////////////////////////////////
function send_command_to_nagios( $command_string ) {

  global $conf;
  
  if ( file_exists ( $conf['nagios_command_file'] ) ) {
    $fifo = fopen( $conf['nagios_command_file'], 'a'); 
    fwrite($fifo, $command_string); 
    fclose($fifo);

  } else {
    return 1;
  }

}

///////////////////////////////////////////////////////////////////////////////
// 
///////////////////////////////////////////////////////////////////////////////
function schedule_service_downtime($hostname, $service, $downtime = 0 )  {
  
  global $conf;
  if ( $downtime == 0 )
    $downtime = $conf['default_downtime_period'];
  $now = time();
  $end_maint = $now + $downtime;
  
  
  $command_string = "[$now] SCHEDULE_SVC_DOWNTIME;$hostname;$service;$now;$end_maint;1;0;$downtime;SCRIPT;Invoked from command line\n";

  send_command_to_nagios( $command_string );
  
}

///////////////////////////////////////////////////////////////////////////////
// 
///////////////////////////////////////////////////////////////////////////////
function schedule_host_service_downtime($hostname, $downtime = 0 )  {
  
  global $conf;
  if ( $downtime == 0 )
    $downtime = $conf['default_downtime_period'];
  $now = time();
  $end_maint = $now + $downtime;
  
  $command_string = "[$now] SCHEDULE_HOST_SVC_DOWNTIME;$hostname;$now;$end_maint;0;0;$downtime;SCRIPT;Invoked from command line\n";

  send_command_to_nagios( $command_string );
  
}

///////////////////////////////////////////////////////////////////////////////
// 
///////////////////////////////////////////////////////////////////////////////
function schedule_host_downtime($hostname, $downtime = 0 )  {
  
  global $conf;
  if ( $downtime == 0 )
    $downtime = $conf['default_downtime_period'];
  $now = time();
  $end_maint = $now + $downtime;
  
  
  $command_string = "[$now] SCHEDULE_HOST_DOWNTIME;$hostname;$now;$end_maint;0;0;$downtime;SCRIPT;Invoked from command line\n";

  send_command_to_nagios( $command_string );
  
}

///////////////////////////////////////////////////////////////////////////////
// 
///////////////////////////////////////////////////////////////////////////////
function enable_service_notifications($hostname, $service )  {
  
  global $conf;

  $now = time();
  
  $command_string = "[$now] ENABLE_SVC_NOTIFICATIONS;$hostname;$service\n";

  send_command_to_nagios( $command_string );
  
}

///////////////////////////////////////////////////////////////////////////////
// 
///////////////////////////////////////////////////////////////////////////////
function disable_service_notifications($hostname, $service )  {
  
  global $conf;

  $now = time();
  
  $command_string = "[$now] DISABLE_SVC_NOTIFICATIONS;$hostname;$service\n";

  send_command_to_nagios( $command_string );
  
}

///////////////////////////////////////////////////////////////////////////////
// 
///////////////////////////////////////////////////////////////////////////////
function disable_all_service_notifications($hostname )  {
  
  global $conf;

  $now = time();
  
  $command_string = "[$now] DISABLE_ALL_NOTIFICATIONS_BEYOND_HOST;$hostname\n";

  send_command_to_nagios( $command_string );
  
}

///////////////////////////////////////////////////////////////////////////////
// 
///////////////////////////////////////////////////////////////////////////////
function disable_host_notifications( $hostname )  {
  
  global $conf;

  $now = time();

  $command_string = "[$now] DISABLE_HOST_NOTIFICATIONS;$hostname\n";

  send_command_to_nagios( $command_string );
  
}

///////////////////////////////////////////////////////////////////////////////
// 
///////////////////////////////////////////////////////////////////////////////
function enable_host_notifications($hostname )  {
  
  global $conf;

  $now = time();

  $command_string = "[$now] ENABLE_HOST_NOTIFICATIONS;$hostname\n";

  send_command_to_nagios( $command_string );
  
}


?>
