<section class="container">
    <?php
    if ($this->op_log) {
        ?>
        <div class="app-styling">
            <?php
            foreach($this->op_log as $field) {
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
    ?>
</section>