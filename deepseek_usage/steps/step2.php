<?php
// Note: all vars have been html encoded at intake.
?>

<h5 class="ul"><?= _('Deepseek Cost Metrics') ?></h5>
<p><?= _('Specify which Deepseek cost metrics you wish to monitor.') ?></p>
<table class="table table-no-border table-auto-width table-padded">
    <tr>
        <td class="vt">
            <input type="checkbox" class="checkbox" name="services[total_cost][monitor]" id="total_cost_monitor" <?= is_checked(grab_array_var($services['total_cost'], "monitor"), "on") ?>>
        </td>
        <td class="vt">
            <div>
                <label for="total_cost_monitor" style="line-height: auto;">
                    <b><?= _('Total Cost') ?></b>
                </label>
                <div style="margin-bottom: 6px;"><?= _('Monitor the total cost this period') ?></div>
            </div>
            <div>
                <label><i title="<?= _('Warning Threshold') ?>" class="material-symbols-outlined md-warning md-18 md-400 md-middle tt-bind">warning</i></label>
                <input type="text" size="8" name="services[total_cost][warning]" value="<?= isset($services['total_cost']['warning']) ? $services['total_cost']['warning'] : '' ?>" class="form-control condensed">
                <?= _('$') ?>&nbsp;&nbsp;
                <label><i title="<?= _('Critical Threshold') ?>" class="material-symbols-outlined md-critical md-18 md-400 md-middle tt-bind">error</i></label>
                <input type="text" size="8" name="services[total_cost][critical]" value="<?= isset($services['total_cost']['critical']) ? $services['total_cost']['critical'] : '' ?>" class="form-control condensed">
                <?= _('$') ?>
            </div>
        </td>
        <td class="vt">
            <div><label><?= _('Time period') ?></label></div>
            <div style="margin-bottom: 6px;"><?= _('e.g 2 week') ?></div>
            <div class="flex" style="margin-top: 6px;">
                <input type="text" size="3" name="services[total_cost][time_units]" id="total_cost_time_units" value="<?= $services['total_cost']['time_units'] ?>" class="form-control condensed">&nbsp;&nbsp;&nbsp;
                <select name="services[total_cost][time_unit]" id="total_cost_time_unit" class="form-control condensed">
                    <option value="day" <?= is_selected($services['total_cost']['time_unit'], "day") ?>>Day</option>
                    <option value="week" <?= is_selected($services['total_cost']['time_unit'], "week") ?>>Week</option>
                    <option value="month" <?= is_selected($services['total_cost']['time_unit'], "month") ?>>Month</option>
                </select>
            </div>
        </td>
    </tr>
</table>

<h5 class="ul"><?= _('Request Metrics') ?></h5>
<p><?= _('Specify which Deepseek request metrics you wish to monitor.') ?></p>
<table class="table table-no-border table-auto-width">
    <tr>
        <td>
            <input type="checkbox" id="requests" class="checkbox" name="services[requests][monitor]" <?= isset($services['requests']['monitor']) ? is_checked(checkbox_binary($services["requests"]["monitor"]), "1") : '' ?>>
        </td>
        <td>
            <label class="normal" for="requests">
                <b><?= _('Daily Requests Usage') ?></b><br>
            </label>
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <div class="pad-t5">
                <label><i title="<?= _('Warning Threshold') ?>" class="material-symbols-outlined md-warning md-18 md-400 md-middle tt-bind">warning</i></label>
                <input type="text" size="8" name="services[requests][warning]" value="<?= isset($services['requests']['warning']) ? $services['requests']['warning'] : '' ?>" class="form-control condensed">&nbsp;&nbsp;
                <label><i title="<?= _('Critical Threshold') ?>" class="material-symbols-outlined md-critical md-18 md-400 md-middle tt-bind">error</i></label>
                <input type="text" size="8" name="services[requests][critical]" value="<?= isset($services['requests']['critical']) ? $services['requests']['critical'] : '' ?>" class="form-control condensed">
            </div>
        </td>
    </tr>
</table>

<h5 class="ul"><?= _('Token Metrics') ?></h5>
<p><?= _('Specify which Deepseek token metrics you wish to monitor.') ?></p>
<table class="table table-no-border table-auto-width">
    <tr>
        <td>
            <input type="checkbox" id="generated_tokens" class="checkbox" name="services[generated_tokens][monitor]" <?= isset($services['generated_tokens']['monitor']) ? is_checked(checkbox_binary($services["generated_tokens"]["monitor"]), "1") : '' ?>>
        </td>
        <td>
            <label class="normal" for="generated_tokens">
                <b><?= _('Daily Generated Token Usage') ?></b><br>
            </label>
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <div class="pad-t5">
                <label><i title="<?= _('Warning Threshold') ?>" class="material-symbols-outlined md-warning md-18 md-400 md-middle tt-bind">warning</i></label>
                <input type="text" size="8" name="services[generated_tokens][warning]" value="<?= isset($services['generated_tokens']['warning']) ? $services['generated_tokens']['warning'] : '' ?>" class="form-control condensed">&nbsp;&nbsp;
                <label><i title="<?= _('Critical Threshold') ?>" class="material-symbols-outlined md-critical md-18 md-400 md-middle tt-bind">error</i></label>
                <input type="text" size="8" name="services[generated_tokens][critical]" value="<?= isset($services['generated_tokens']['critical']) ? $services['generated_tokens']['critical'] : '' ?>" class="form-control condensed">
            </div>
        </td>
    </tr>
</table>

<table class="table table-no-border table-auto-width">
    <tr>
        <td>
            <input type="checkbox" id="context_tokens" class="checkbox" name="services[context_tokens][monitor]" <?= isset($services['context_tokens']['monitor']) ? is_checked(checkbox_binary($services["context_tokens"]["monitor"]), "1") : '' ?>>
        </td>
        <td>
            <label class="normal" for="context_tokens">
                <b><?= _('Daily Context Token Usage') ?></b><br>
            </label>
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <div class="pad-t5">
                <label><i title="<?= _('Warning Threshold') ?>" class="material-symbols-outlined md-warning md-18 md-400 md-middle tt-bind">warning</i></label>
                <input type="text" size="8" name="services[context_tokens][warning]" value="<?= isset($services['context_tokens']['warning']) ? $services['context_tokens']['warning'] : '' ?>" class="form-control condensed">&nbsp;&nbsp;
                <label><i title="<?= _('Critical Threshold') ?>" class="material-symbols-outlined md-critical md-18 md-400 md-middle tt-bind">error</i></label>
                <input type="text" size="8" name="services[context_tokens][critical]" value="<?= isset($services['context_tokens']['critical']) ? $services['context_tokens']['critical'] : '' ?>" class="form-control condensed">
            </div>
        </td>
    </tr>
</table>