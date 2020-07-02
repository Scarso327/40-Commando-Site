<section class = "container">
    <div class="profile-body">
        <div class="col">
            <div class = "steam-pfp-box" title="Attendance">
                <img class = "steam-pfp" src="<?=$this->member->steampfplarge;?>" height="128" width="128">
                100%
            </div>
            <h3 class="name"><?=Member::getFriendlyName($this->member->first_name, $this->member->last_name, $this->member->steamName);?></h3>
            <?php
            if ($this->member->is_in_unit == 1) {
                if ($this->member->assignment == -1) {
                    $assignment = ((object) array (
                        "name" => "Unassigned",
                        "orbat_name" => "Unassigned"
                    ));
                } else {
                    $assignment = Assignments::getAssignment($this->member->assignment);
                }
                ?>
                <h4 title="Join Date: <?=date('d/m/Y', strtotime($this->member->join_date));?>, Rank Change Date: <?=date('d/m/Y', strtotime($this->member->rank_change_date));?>"><?=(($this->member->is_acting == 1) ? "Acting " : "").(Application::getRankInfo($this->member->rank, (($assignment) ? $assignment->name : "")))->name;?>, <?=$assignment->orbat_name;?></h4>
                <?php
            } else {
                ?>
                <h4 title="Discharge Date: <?=date('d/m/Y', strtotime($this->member->left_date));?>">Discharged</h4>
                <?php
            }

            if (isset($this->buttons)) {
                foreach ($this->buttons as $button) {
                    echo '<a '.((($button["colour"] != "") ? "style='background-color: ".$button["colour"].";'": "")).'onclick="showModal(this)" data-id="'.$button["id"].'" data-name="'.$button["name"].'" class="action-button">'.$button["action_name"].'</a>';
                }
            }
            ?>
        </div>
        <div class="col main">
            <?php
            if ($this->member->is_in_unit == 1) {
                ?>
                <div class = "tab-buttons">
                    <a class="tab-button<?php if ($this->subpage == "") {?> active<?php } ?>" href="<?=URL;?>unit/personnel/<?=$this->member->steamid;?>">History</a>
                    <a class="tab-button<?php if ($this->subpage == "awards") {?> active<?php } ?>" href="<?=URL;?>unit/personnel/<?=$this->member->steamid;?>/awards">Awards</a>
                </div>
                <?php
            }
            ?>
            <?php
            switch ($this->subpage) {
                case '':
                    include "history.php";
                    break;
                case 'awards':
                    include "awards.php";
                    break;
            }
            ?>
        </div>
    </div>
</section>