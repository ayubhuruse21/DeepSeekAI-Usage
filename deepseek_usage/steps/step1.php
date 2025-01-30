<?php
// Note: all vars have been html encoded at intake.
?>

<script>
    $(function(){
        $(".helptooltip").popover({ html: true });
    });
</script>

<h5 class="ul"><?= _('Host Info') ?></h5>
<table class="table table-condensed table-no-border table-auto-width">
    <tr>
        <td class="vt">
            <label><?= _('Host Name') ?>:</label>
        </td>
        <td>
            <input type="text" size="40" name="hostname" id="hostname" value="<?= $hostname ?>" class="form-control">
            <div class="subtext"><?= _('This wizard does not monitor a host, but you need a host name under which you can find your Deepseek metrics.') ?></div>
        </td>
    </tr>
</table>

<h5 class="ul"><?= _('Deepseek Account Information') ?></h5>
<table class="table table-condensed table-no-border table-auto-width">
    <tr>
        <td class="vt">
            <label><?= _('API Key') ?>:</label>
        </td>
        <td>
            <input type="text" autocomplete="off" size="40" name="api_key" id="api_key" value="<?= $api_key ?>" class="form-control">
            <div class="subtext"><?= _('The API key for the Deepseek account you would like to monitor.') ?></div>
        </td>
    </tr>
    <tr>
        <td class="vt">
            <label><?= _('Organization ID') ?>:</label>
        </td>
        <td>
            <input type="text" autocomplete="off" size="40" name="org_id" id="org_id" value="<?= $org_id ?>" class="form-control">
            <div class="subtext"><?= _('Your Deepseek organization ID.') ?></div>
        </td>
    </tr>
    <tr>
        <td class="vt">
            <label><?= _('Session Token') ?>:</label>
            <i class="helptooltip fa fa-question-circle fa-14" data-placement="right" data-content="The session token must be copied from the browser inspector when logged into your Deepseek account on the web.
            <ul>
                <li>Log in to Deepseek, and navigate to <a target='_blank' href='https://platform.deepseek.com/account/usage'>Usage</a></li>
                <li>Open your inspector and choose the network tab.</li>
                <li>Select usage from the list of resources loaded by the browser.</li>
                <li>Click the headers subtab.</li>
                <li>Copy the text beginning with 'sess-' which follows 'Authorization: Bearer...'</li>
            </ul>
            Note: this token will expire occasionally and need to be updated."></i>
        </td>
        <td>
            <input type="text" autocomplete="off" size="40" name="sess_tkn" id="sess_tkn" value="<?= $sess_tkn ?>" class="form-control">
            <div class="subtext"><?= _('The session token taken from your browser when viewing your Deepseek statistics.') ?></div>
        </td>
    </tr>
</table>