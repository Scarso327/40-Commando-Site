<section class="container">
    <?php
    $app = Applications::getApplication($this->record->app_id);

    if (!$app) {
        new DisplayError("#500", true);
        exit;
    }

    switch (true) {
        case ($app->accepted == 1):
            if ($this->record->interview_id == -1) {
                ?>
                <h2>Welcome to the interview stage!</h2>
                <p>The interview stage is used to assess your character and provide you a chance to ask any questions you may have. It will be conducted by a member of our HQ Element on <a target="_blank" href="ts3server://scarso.dev/">TeamSpeak</a> when you're available, just join the "Waiting for Interview" channel after connecting.</p>
                <p>Once this stage has been completed, successfully, you'll be granted the rank of <a target="_blank" href="<?=URL;?>structure/#1-rec">Recruit</a> and be expected to complete your All Arms Commando Course before joining us on operation for your assessment.</p>
                <?php
            } else {
                ?>
                <h2>Your interview has been completed!</h2>
                <p>You have passed and completed your interview. You've been granted the rank of <a target="_blank" href="<?=URL;?>structure/#1-rec">Recruit</a>.</p>
                <?php
            }
            break;
        default:
            ?>
            <h2>Your application hasn't been accepted!</h2>
            <p>Your application must be accepted before you can proced any further.</p>
            <?php
    }
    ?>
</section>