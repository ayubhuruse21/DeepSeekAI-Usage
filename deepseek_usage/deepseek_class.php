<?php

class Deepseek {
    public $base_url   = "https://api.deepseek.com";
    public $api_token  = "";
    public $org_id     = "";
    public $debug      = false;
    public $throttle   = 15; // Seconds to wait before next request
    public $error      = '';
    public $endpts;
    public $api_v      = 'v1'; // API version
    public $uglify     = true; // Whether to uglify JSON responses

    public function __construct($base_url = '', $api_token = '', $org_id = '', $debug = false) {
        $this->base_url = ($base_url) ? $base_url : $this->base_url;
        $this->api_token = ($api_token) ? $api_token : $this->api_token;
        $this->org_id = ($org_id) ? $org_id : $this->org_id;
        $this->debug = ($debug) ? $debug : $this->debug;

        $this->setEndPoints();
    }

    public function setEndPoints() {
        $this->endpts = (object) [
            "UsageForDate"  => "{$this->api_v}/usage",
            "CostForPeriod" => "dashboard/billing/usage",
            "Subscription"  => "dashboard/billing/subscription",
            "Billing"       => "account/billing/overview",
            "RateLimits"    => "dashboard/rate_limits",
        ];
    }

    public function getTotalCost($period) {
        list($startDate, $endDate) = $this->periodToDates($period);
        $days = $this->datesToDays($startDate, $endDate);

        if ($days > 100) {
            nagios_exit("Error: Period exceeds maximum of 100 days ($days days).", STATUS_UNKNOWN);
        }

        $response = $this->getCostForPeriod($startDate, $endDate);
        if (! $response) {
            nagios_exit($this->error, STATUS_UNKNOWN);
        }
        $response = json_decode($response, true);

        return $response['total_usage'];
    }

    public function getDailyTokens($date = '') {
        $dateTime = new DateTime();
        $today = $dateTime->format("Y-m-d");
        $date = ($date) ? $date : $today;
        $cols = ['n_requests', 'n_context_tokens_total', 'n_generated_tokens_total'];

        $response = $this->getUsageForDate($date);
        if (! $response) {
            nagios_exit($this->error, STATUS_UNKNOWN);
        }
        $response = json_decode($response, true);

        foreach ($cols as $col) {
            $sums[$col] = 0;
        }

        if ($response['data']) {
            $sums = $this->sumColumns($response['data'], $cols);
        }
        return $sums;
    }

    public function doGet($endpoint) {
        $rq = new apiRequest($this->base_url, $this->org_id, $this->debug);
        $response = $rq->doRequest($this->api_token, 'GET', $endpoint, false, $this->uglify);

        if (!$rq->error) {
            return $response;
        } else {
            $this->error = $this->formatError($rq);
            return false;
        }
    }

    public function doPost($endpoint, $data, $use_org_id = false) {
        $rq = new apiRequest($this->base_url, $this->org_id, $this->debug);
        $response = $rq->doRequest($this->api_token, 'POST', $data, $use_org_id, $this->uglify);

        if (!$rq->error) {
            return $response;
        } else {
            $this->error = $this->formatError($rq);
            return false;
        }
    }

    public function periodToDates($period) {
        list($cnt, $unit) = $this->parsePeriod($period);

        $dateTime = new DateTime();
        $endDate = $dateTime->format("Y-m-d");
        $dateTime->modify("-$cnt $unit");
        $startDate = $dateTime->format("Y-m-d");
        return [$startDate, $endDate];
    }

    public function datesToDays($startDate, $endDate) {
        $days = date_diff(new DateTime($endDate), new DateTime($startDate))->days;
        return $days;
    }

    public function parsePeriod($period) {
        if (strpos($period, '_')) {
            list($cnt, $unit) = explode('_', rtrim($period, 's'));
        } else {
            list($cnt, $unit) = [1, rtrim($period, 's')];
        }
        return [$cnt, $unit];
    }

    public function sumColumns($a, $cols) {
        $result = [];
        foreach ($cols as $col) {
            $result[$col] = array_sum(array_column($a, $col));
        }
        return $result;
    }

    public function formatError($rq_obj, $debug = false) {
        $response = "\nError: {$rq_obj->error}\n";

        if ($debug) {
            $response .= "Url: {$rq_obj->url}\n";
            $response .= "Header: " . print_r($rq_obj->header, true) . "\n";
        }
        return $response;
    }
}