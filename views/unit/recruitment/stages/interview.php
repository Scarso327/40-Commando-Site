<h3 class="app-header">
    Past Interviews
    <span class="app-info">
        <?php
        if ($this->record->interview_id == -1 && $this->member->is_in_unit == 0) {
            ?>
            <a title="Add Interview Attempt" onclick="showModal(this)" data-id='5' data-name='Interview'><i class="fas fa-plus-circle"></i></a>
            <a title="Pass Stage" onclick="showModal(this)" data-id='6' data-name='Pass Interview'><i class="fas fa-check-circle"></i></a>
            - INCOMPLETE
            <?php
        } else {
            echo "COMPLETED";
        }
        ?>
    </span>
</h3>
<?=View::buildHistoryTable($this->interviews);?>