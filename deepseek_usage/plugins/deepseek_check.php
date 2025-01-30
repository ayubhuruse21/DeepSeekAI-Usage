#!/usr/bin/env php
<?php

$PROGRAM = 'deepseek_check.php';
define("PROGRAM", $PROGRAM);
$VERSION = '1.0.0';
define("VERSION", $VERSION);
define("STATUS_OK", 0);
define("STATUS_WARNING", 1);
define("STATUS_CRITICAL", 2);
define("STATUS_UNKNOWN", 3);
define("DEBUG", false);

$args = new Args();

$args->addArg(new ARG('k', 'api_key', '', true, 'Your Deepseek API Key', 'sk-...', 'string'));
$args->addArg(new ARG('m', 'metric', '', true, 'The Deepseek metric you want to monitor', 'total_cost', 'string'));
$args->addArg(new ARG('o', 'org_id', '', true, 'Your Deepseek organization ID', 'org-...', 'string'));
$args->addArg(new ARG('t', 'period', '', false, 'The period for which you want to aggregate data', '5_day, week, 2_month', 'string'));
$args->addArg(new ARG('c', 'crit', '', true, 'Critical threshold value', '100', 'string'));
$args->addArg(new ARG('w', 'warn', '', true, 'Warning threshold value', '90', 'string'));

if (!$args->makeOpts()) {
    return;
}

switch ($args->opts['metric']) {
    case 'total_cost':
        $ds = new Deepseek('', $args->opts['api_key'], $args->opts['org_id'], DEBUG);
        $total_cost = $ds->getTotalCost($args->opts['period']);
        $total_cost = round($total_cost/100, 2);

        $crit = $args->opts['crit'];
        $warn = $args->opts['warn'];
        $period = $args->opts['period'];
        if ($total_cost > $crit) {
            nagios_exit("CRITICAL - Deepseek total cost, \$$total_cost, has exceeded \$$crit for the specified period ($period).\n\n", STATUS_CRITICAL);
        } elseif ($total_cost > $warn) {
            nagios_exit("WARNING - Deepseek total cost, \$$total_cost, has exceeded \$$warn for the specified period ($period).\n\n", STATUS_WARNING);
        } else {
            nagios_exit("OK - Deepseek total cost, \$$total_cost, is below thresholds for the specified period ($period).\n\n", STATUS_OK);
        }
        break;

    default:
        nagios_exit("Error: {$args->opts['metric']} is not a valid metric.", STATUS_UNKNOWN);
        break;
}> <?php
> 
> class Deepseek {
>     public $base_url   = "https://api.deepseek.com";
>     public $api_token  = "";
>     public $org_id     = "";
>     public $debug      = false;
>     public $throttle   = 15; // Seconds to wait before next request
>     public $error      = '';
>     public $endpts;
> 
>     public function __construct($base_url = '', $api_token = '', $org_id = '', $debug = false) {
>         $this->base_url = ($base_url) ? $base_url : $this->base_url;
>         $this->api_token = ($api_token) ? $api_token : $this->api_token;
>         $this->org_id = ($org_id) ? $org_id : $this->org_id;
>         $this->debug = ($debug) ? $debug : $this->debug;
> 
>         $this->setEndPoints();
>     }
> 
>     public function setEndPoints() {
>         $this->endpts = (object) [
>             "UsageForDate"  => "{$this->api_v}/usage",
>             "CostForPeriod" => "dashboard/billing/usage",
>             "Subscription"  => "dashboard/billing/subscription",
>             "Billing"       => "account/billing/overview",
>             "RateLimits"    => "dashboard/rate_limits",
>         ];
>     }
> 
>     public function getTotalCost($period) {
>         list($startDate, $endDate) = $this->periodToDates($period);
>         $days = $this->datesToDays($startDate, $endDate);
> 
>         if ($days > 100) {
>             nagios_exit("Error: Period exceeds maximum of 100 days ($days days).", STATUS_UNKNOWN);
>         }
> 
>         $response = $this->getCostForPeriod($startDate, $endDate);
>         if (! $response) {
>             nagios_exit($this->error, STATUS_UNKNOWN);
>         }
>         $response = json_decode($response, true);
> 
>         return $response['total_usage'];
>     }
> 
>     public function getDailyTokens($date = '') {
>         $dateTime = new DateTime();
>         $today = $dateTime->format("Y-m-d");
>         $date = ($date) ? $date : $today;
>         $cols = ['n_requests', 'n_context_tokens_total', 'n_generated_tokens_total'];
> 
>         $response = $this->getUsageForDate($date);
>         if (! $response) {
>             nagios_exit($this->error, STATUS_UNKNOWN);
>         }
>         $response = json_decode($response, true);
> 
>         foreach ($cols as $col) {
>             $sums[$col] = 0;
>         }
> 
>         if ($response['data']) {
>             $sums = $this->sumColumns($response['data'], $cols);
>         }
>         return $sums;
>     }
> 
>     public function doGet($endpoint) {
>         $rq = new apiRequest($this->base_url, $this->org_id, $this->debug);
>         $response = $rq->doRequest($this->api_token, 'GET', $endpoint, false, $this->uglify);
> 
>         if (!$rq->error) {
>             return $response;
>         } else {
>             $this->error = $this->formatError($rq);
>             return false;
>         }
>     }
> 
>     // Other utility methods like periodToDates, datesToDays, etc.
> }

