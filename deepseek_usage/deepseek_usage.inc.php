<?php
//
// Deepseek Usage Wizard
// Copyright (c) 2023-2023 Nagios Enterprises, LLC. All rights reserved.
//

include_once(dirname(__FILE__) . '/../configwizardhelper.inc.php');
include_once(dirname(__FILE__) . '/deepseek_class.php');

deepseek_usage_configwizard_init();

function deepseek_usage_configwizard_init()
{
    $name = "deepseek_usage";
    $args = array(
        CONFIGWIZARD_NAME => $name,
        CONFIGWIZARD_VERSION => "1.0.1",
        CONFIGWIZARD_TYPE => CONFIGWIZARD_TYPE_MONITORING,
        CONFIGWIZARD_DESCRIPTION => _("Monitor your usage on Deepseek."),
        CONFIGWIZARD_DISPLAYTITLE => _("Deepseek Usage"),
        CONFIGWIZARD_FUNCTION => "deepseek_usage_configwizard_func",
        CONFIGWIZARD_PREVIEWIMAGE => "deepseek.png",
        CONFIGWIZARD_FILTER_GROUPS => array(''),
        CONFIGWIZARD_REQUIRES_VERSION => 60030
    );
    register_configwizard($name, $args);
}

/**
 * @param string $mode
 * @param null   $inargs
 * @param        $outargs
 * @param        $result
 *
 * @return string
 */
function deepseek_usage_configwizard_func($mode = "", $inargs = null, &$outargs = null, &$result = null)
{
    // Grab core wizard info
    $wizard_name = grab_array_var($inargs, "wizard");
    $wiz_args = get_configwizard_by_name($wizard_name);
    $wizard_title = isset($wiz_args[CONFIGWIZARD_DISPLAYTITLE]) ? $wiz_args[CONFIGWIZARD_DISPLAYTITLE] : '';

    // Gotta have a sesh
    if (!isset($_SESSION[$wizard_name])) {
        $_SESSION[$wizard_name] = [];
    }

    // Give session a simple name for convenience
    $sess =& $_SESSION[$wizard_name];

    // $inargs keys that needn't/can't be encoded with htmlentities()
    $noEncodeList = [];

    // Array elements that may have user macros
    $userMacroKeys = [];

    // Initialize return code and output
    $result = 0;
    $output = "";

    // Initialize output args - pass back the same data we got
    $outargs[CONFIGWIZARD_PASSBACK_DATA] = $inargs;

    $services_default = [
        "total_cost" => [
            'monitor' => 'off',
            'warning' => '',
            'critical' => '',
            'time_units' => 1,
            'time_unit' => 'week',
        ],

        "generated_tokens" => [
            'monitor' => 'off',
            'warning' => '',
            'critical' => '',
        ],

        "context_tokens" => [
            'monitor' => 'off',
            'warning' => '',
            'critical' => '',
        ],

        "requests" => [
            'monitor' => 'off',
            'warning' => '',
            'critical' => '',
        ],

    ];

    switch ($mode) {
        case CONFIGWIZARD_MODE_GETSTAGE1HTML:

            // Clear session if this is the first wizard run
            if ($_POST == []) {
                unset($_SESSION[$wizard_name]);
                $_SESSION[$wizard_name] = [];
                $sess =& $_SESSION[$wizard_name];
            }

            // Session page numbers are used to determine direction to prevent validation onback button
            $sess['page'] = 1;
            encode_form_all($inargs, $noEncodeList); // Encode all user vars

            // Remove values from session that aren't applicable here (for back-button response)
            $retain = [];
            // clear_sess_vars($sess, $retain);

            $hostname = grab_in_var($inargs, "hostname", "", $sess);
            $api_key = grab_in_var($inargs, "api_key", "", $sess);
            $org_id = grab_in_var($inargs, "org_id", "", $sess);
            $sess_tkn = grab_in_var($inargs, "sess_tkn", "", $sess);

            // Retain Step 2 data after back button
            $services = grab_in_var($inargs, "services", $services_default, $sess);

            ob_start();
            include __DIR__ . '/steps/step1.php';
            $output = ob_get_clean();

            break;

        case CONFIGWIZARD_MODE_VALIDATESTAGE1DATA:
            $hostname = grab_in_var($inargs, "hostname", "", $sess);
            $api_key = grab_in_var($inargs, "api_key", "", $sess);
            $org_id = grab_in_var($inargs, "org_id", "", $sess);
            $sess_tkn = grab_in_var($inargs, "sess_tkn", "", $sess);

            $errors = [];

            // Only validate if going forward. No need to do so going backward.
            if ($sess['page'] == 1) {
                if (have_value($hostname) == false) {
                    $errors[] = _("No Hostname specified.");
                }
                if (is_valid_host_name($hostname) == false) {
                    $errmsg[] = "Invalid host name.";
                }
                if (have_value($api_key) == false) {
                    $errors[] = _("No API Key specified.");
                }
                if (have_value($org_id) == false) {
                    $errors[] = _("No Organization ID specified.");
                }
                if (have_value($sess_tkn) == false) {
                    $errors[] = _("No Session Token specified.");
                }

                if (! count($errors)) {
                    $ds = new Deepseek('', $api_key, $org_id, $sess_tkn);
                    if (! $ds->getSubscription()) {
                        $errors[] = _("Session Token is incorrect or expired. Please log in to your Deepseek account and get a new session token.");
                    } else {
                        if (! $ds->getFeatures()) {
                            $errors[] = _("Organization ID is incorrect.");
                        }
                    }
                    if (! $ds->testChat()) {
                        $errors[] = _("API Key is incorrect.");
                    }
                }
            }

            if (count($errors)) {
                $outargs[CONFIGWIZARD_ERROR_MESSAGES] = $errors;
                $result = 1;
            }

            break;

        case CONFIGWIZARD_MODE_GETSTAGE2HTML:
            $sess['page'] = 2;
            encode_form_all($inargs, $noEncodeList); // Encode all user vars

            $hostname = grab_in_var($inargs, "hostname", "", $sess);
            $api_key = grab_in_var($inargs, "api_key", "", $sess);
            $org_id = grab_in_var($inargs, "org_id", "", $sess);
            $sess_tkn = grab_in_var($inargs, "sess_tkn", "", $sess);
            $services = grab_in_var($inargs, "services", $services_default, $sess);

            ob_start();
            include __DIR__ . '/steps/step2.php';
            $output = ob_get_clean();
            break;

        case CONFIGWIZARD_MODE_VALIDATESTAGE2DATA:
            $hostname = grab_in_var($inargs, "hostname", "", $sess);
            $api_key = grab_in_var($inargs, "api_key", "", $sess);
            $org_id = grab_in_var($inargs, "org_id", "", $sess);
            $sess_tkn = grab_in_var($inargs, "sess_tkn", "", $sess);
            $services = grab_in_var($inargs, "services", "", $sess);

            $errors = [];

            // Only validate if going forward. No need to do so going backward.
            if ($sess['page'] == 2) {

                $has_service = false;
                foreach($services as $service) {
                    if (isset($service['monitor']) && $service['monitor'] == 'on') {
                        $has_service = true;
                        break;
                    }
                }
                if (!$has_service) {
                    $errors[] = _("You must select at least one service for monitoring.");
                }

                foreach ($services as $service => $args) {
                    // Make key pretty for use in warnings
                    $service_pretty = str_replace('_', ' ', $service);
                    if (isset($args["monitor"]) && $args["monitor"] == "on") {

                        if ($service == 'total_cost') {
                            if (have_value($args['time_units']) == false) {
                                $errors[] = _("No Time Units specified.");
                            }
                            if (!is_numeric($args['time_units'])) {
                                $errors[] = _("Time Units must be numeric.");
                            }
                            if (have_value($args['time_unit']) == false) {
                                $errors[] = _("No Time Unit specified.");
                            }

                            $ds = new Deepseek('', $api_key, $org_id, $sess_tkn);
                            list($startDate, $endDate) = $ds->periodToDates($args['time_units'] . "_" . $args['time_unit']);
                            $days = $ds->datesToDays($startDate, $endDate);
                            if ($days > 100) {
                                $errors[] = _("Period exceeds maximum of 100 hundred days ($days days).");
                            }
                        }

                        if ($args["warning"] === null || $args["warning"] === '' || $args["critical"] === null || $args["critical"] === '') {
                            $errors[] = _("$service_pretty: CPU Warning and Critical values are required.");
                        }
                        if (!is_numeric($args["warning"]) || !is_numeric($args["critical"])) {
                            $errors[] = _("$service_pretty: CPU Warning and Critical values must be numeric.");
                        }
                        if ($args["warning"] < 0 || $args["critical"] < 0) {
                            $errors[] = _("$service_pretty: CPU Warning and Critical values must be positive.");
                        }
                    }
                }
            }

            if (count($errors)) {
                $outargs[CONFIGWIZARD_ERROR_MESSAGES] = $errors;
                $result = 1;
            }

            break;

        case CONFIGWIZARD_MODE_GETSTAGE3HTML:
            $sess['page'] = 3;
            encode_form_all($inargs, $noEncodeList); // Encode all user vars

            $hostname = grab_in_var($inargs, "hostname", "", $sess);
            $api_key = grab_in_var($inargs, "api_key", "", $sess);
            $org_id = grab_in_var($inargs, "org_id", "", $sess);
            $sess_tkn = grab_in_var($inargs, "sess_tkn", "", $sess);
            $services = grab_in_var($inargs, "services", "", $sess);

            $output = '';

            break;

        case CONFIGWIZARD_MODE_VALIDATESTAGE3DATA:

            break;

        case CONFIGWIZARD_MODE_GETFINALSTAGEHTML:

            break;

        case CONFIGWIZARD_MODE_GETOBJECTS:
            $hostname = grab_in_var($inargs, "hostname", "", $sess);
            $api_key = grab_in_var($inargs, "api_key", "", $sess);
            $org_id = grab_in_var($inargs, "org_id", "", $sess);
            $sess_tkn = grab_in_var($inargs, "sess_tkn", "", $sess);
            $services = grab_in_var($inargs, "services", "", $sess);

            $meta_arr = array();
            $meta_arr["hostname"] = $hostname;
            $meta_arr["api_key"] = $api_key;
            $meta_arr["org_id"] = $org_id;
            $meta_arr["sess_tkn"] = $sess_tkn;
            $meta_arr["services"] = $services;
            save_configwizard_object_meta($wizard_name, $hostname, "", $meta_arr);

            $objs = array();

            if (!host_exists($hostname)) {
                $objs[] = array(
                    "type" => OBJECTTYPE_HOST,
                    "use" => "xiwizard_linuxserver_host",
                    "host_name" => $hostname,
                    "address" => 'deepseek.com',
                    "icon_image" => "deepseek.png",
                    "statusmap_image" => "deepseek.png",
                    "_xiwizard" => $wizard_name,
                );
            }

            // common plugin opts
            $commonopts = "--api_key $api_key --org_id $org_id --sess_tkn $sess_tkn ";

            foreach ($services as $service => $args) {
                switch ($service) {
                    case "total_cost":
                        if ($args["monitor"] != "on") {
                            break;
                        }

                        $checkcommand = "check_deepseek! ";
                        $checkcommand .= $commonopts;
                        $checkcommand .= "--metric " . "total_cost" . " ";
                        $checkcommand .= "--period " . $args["time_units"] . '_' . $args["time_unit"] . " ";
                        $checkcommand .= "--warn " . $args["warning"] . " --crit " . $args["critical"] . " ";

                        $objs[] = array(
                            "type" => OBJECTTYPE_SERVICE,
                            "host_name" => $hostname,
                            "service_description" => "Deepseek Total Cost",
                            "use" => "generic-service",
                            "check_command" => $checkcommand,
                            "_xiwizard" => $wizard_name,
                        );

                        break;

                    case "generated_tokens":
                        if ($args["monitor"] != "on") {
                            break;
                        }

                        $checkcommand = "check_deepseek! ";
                        $checkcommand .= $commonopts;
                        $checkcommand .= "--metric " . "generated_tokens" . " ";
                        $checkcommand .= "--warn " . $args["warning"] . " --crit " . $args["critical"] . " ";

                        $objs[] = array(
                            "type" => OBJECTTYPE_SERVICE,
                            "host_name" => $hostname,
                            "service_description" => "Deepseek Generated Tokens",
                            "use" => "generic-service",
                            "check_command" => $checkcommand,
                            "_xiwizard" => $wizard_name,
                        );

                        break;

                    case "context_tokens":
                        if ($args["monitor"] != "on") {
                            break;
                        }

                        $checkcommand = "check_deepseek! ";
                        $checkcommand .= $commonopts;
                        $checkcommand .= "--metric " . "context_tokens" . " ";
                        $checkcommand .= "--warn " . $args["warning"] . " --crit " . $args["critical"] . " ";

                        $objs[] = array(
                            "type" => OBJECTTYPE_SERVICE,
                            "host_name" => $hostname,
                            "service_description" => "Deepseek Context Tokens",
                            "use" => "generic-service",
                            "check_command" => $checkcommand,
                            "_xiwizard" => $wizard_name,
                        );

                        break;

                    case "requests":
                        if ($args["monitor"] != "on") {
                            break;
                        }

                        $checkcommand = "check_deepseek! ";
                        $checkcommand .= $commonopts;
                        $checkcommand .= "--metric " . "requests" . " ";
                        $checkcommand .= "--warn " . $args["warning"] . " --crit " . $args["critical"] . " ";

                        $objs[] = array(
                            "type" => OBJECTTYPE_SERVICE,
                            "host_name" => $hostname,
                            "service_description" => "Deepseek Requests",
                            "use" => "generic-service",
                            "check_command" => $checkcommand,
                            "_xiwizard" => $wizard_name,
                        );

                        break;

                    default:

                        break;
                }
            }

            $outargs[CONFIGWIZARD_NAGIOS_OBJECTS] = $objs;
            break;

        default:

            break;
    }

    return $output;
}