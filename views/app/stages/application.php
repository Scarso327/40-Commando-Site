<?php
Forms::$steamidOverride = Account::$steamid;

if ($this->record->app_id == -1) {
    if (!isset($this->page_forms)) {
        new DisplayError("#500", true);
        exit;
    }
    
    $wasLastAppDeclined = Applications::wasLastAppDeclined(Account::$steamid);

    if ($wasLastAppDeclined) {
        $app = Applications::getApplication($wasLastAppDeclined);
        ?>
        <section class="container">
            <h2>Your last application was declined!</h2>
            <p>You successfully submitted an application but it was declined at <?=date( 'H:i d/m/Y', strtotime($app->last_response) );?>. If you want feedback or reasoning as to why this decision was made you may contact a member of our command on <a target="_blank" href="https://discord.gg/zGB9xsg">Discord</a>. You may reapply below at anytime.</p>
        </section>
        <?php
    }
    ?>
    <section class = "container form">
        <h2>Application Form</h2>
        <p style="margin-bottom: 20px;">Once you've filled out this form a member of our unit's command will review it to ensure you meet our requirements. Ensure you read each field's short description as it may provide requirements for you answer to follow.</p>
        <?=Forms::buildForm($this->page_forms[0][0], $this->page_forms[0][1], URL."app/?stage=application&form");?>
    </section>
    <?php
} else {
    $app = Applications::getApplication($this->record->app_id);

    if (!$app) {
        new DisplayError("#500", true);
        exit;
    }

    ?>
    <section class="container">
    <?php

    if ($app->accepted == 1) {
        ?>
        <h2>Your application has been accepted!</h2>
        <p>Your application has been accepted. You may now proced to the interview stage of recruitment.</p>
        <?php
    } else {
        ?>
        <h2>You've submitted your application!</h2>
        <p>You have successfully submitted your application. A member of our command will respond shortly and the result will be visible here.</p>
        <?php
    }
    ?>
    </section>
    <?php
}
?>