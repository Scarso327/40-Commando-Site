<?php
if ($this->app) {
    ?>
    <h3 class="app-header">
        Application Responses
        <span class="app-info">
            <?php
            if ($this->app->accepted == 0 && $this->app->declined == 0 && $this->member->is_in_unit == 0) {
                ?>
                <a title="Add Note" onclick="showModal(this)" data-id='4' data-name='Application Note'><i class="fas fa-plus-circle"></i></a>
                <a title="Accept Application" onclick="showModal(this)" data-id='2' data-name='Application Acceptance'><i class="fas fa-check-circle"></i></a>
                <a title="Decline Application" onclick="showModal(this)" data-id='3' data-name='Application Rejection'><i class="fas fa-times-circle"></i></a>
                <?php
            }
            ?>
            #<?=$this->app->id;?> - <?=strtoupper(Applications::getApplicationStatus($this->app));?>
        </span>
    </h3>
    <?php
    /*
    if ($this->app_answers) {
        ?>
        <div class="app-styling">
            <?php
            foreach($this->app_answers as $field) {
                ?>
                <h4><?=$field->name;?></h4>
                <p><?=$field->value;?></p>
                <?php
            }
            ?>
        </div>
        <?php
    } else {
        new DisplayError("#500", false, true);
    }
    */
    View::buildHistoryTable($this->app_responses);
} else {
    ?>
    <h3 class="app-header">
        Waiting on the Applicant...
    </h3>
    <?php
    $wasLastAppDeclined = Applications::wasLastAppDeclined($this->member->steamid);

    if ($wasLastAppDeclined) {
        $app = Applications::getApplication($wasLastAppDeclined);
        ?>
        <p>The Applicant submitted an application but it was declined at <?=date( 'H:i d/m/Y', strtotime($app->last_response) );?>.</p>
        <?php
    }
}
?>