<form class="form" autocomplete="off" method="GET" action="<?=URL.'unit/personnel/'.$this->member->steamid;?>">
    <div class="form-group" style = "display: flex;">
        <select name="submit-type" id = "subtypeDropdown">
            <option value="member">Forms</option>
            <option value="actioner">Submitted Forms</option>
        </select>
        <select style="margin-left: 5px;" name="type" id = "typeDropdown">
            <option value="all">All Types</option>
            <?php
            $types = Form::getFormTypes();
            sort($types);

            foreach ($types as $type) {
                echo '<option value="'.$type.'">'.$type.'</option>';
            }
            ?>
            <option value="other">Other</option>
        </select>
    </div>

    <div class="form-group" style = "display: flex;">
        <input style = "margin-right: 5px;" type="date" id="start" name="start-time" value="<?=$this->history['dates'][0];?>" min="2020-01-01" max="<?=date('Y-m-d')?>">
        <input type="date" id="start" name="end-time" value="<?=$this->history['dates'][1];?>" min="2020-01-01" max="<?=date('Y-m-d')?>">
    </div>

    <button style = "width: 100%; margin: 0px;" type="submit">Search History</button>
</form>
<?php
if (isset($this->history['submit-type']) && isset($this->history['type'])) {
    ?>
    <div id = "dropdownScript">
        <script>
            document.getElementById("subtypeDropdown").value = '<?=$this->history['submit-type'];?>';
            document.getElementById("typeDropdown").value = '<?=$this->history['type'];?>';
            var target = document.getElementById('dropdownScript');
            target.remove( target.childNodes[0] );
        </script>
    </div>
    <?php
}

$type = "actioner";
if ($this->history['submit-type'] == "actioner") { $type = "member"; };

View::buildHistoryTable($this->history["logs"], $type);
?>