<?php
if (isset($this->records)) {
    ?>
    <section class="container">
        <div class = "tab-buttons">
            <a class="tab-button" onclick="filterApps(this, 'Waiting for Application')">Waiting for Application</a>
            <a id="default" class="tab-button active" onclick="filterApps(this, 'In Progress')">In Progress</a>
            <a class="tab-button" onclick="filterApps(this, 'Completed')">Completed</a>
            <a class="tab-button" onclick="filterApps(this, 'Archived')">Archived</a>
        </div>
    </section>
    <section class="container">
        <table id = "table">
            <tr class = "first">
                <th COLSPAN = 6>Details</th>
                <th>Miscellaneous</th>
            </tr>
            <tr class = "second">   
                <!--- Details --->
                <th>Date</th>
                <th>Candidate</th>
                <th COLSPAN = 4>Progress Indication</th>
                <th></th>
            </tr>
            <?php
            function printStatus($stage = "Incomplete") {
                $string = 'class="progress-info';

                switch ($stage) {
                    case "Completed":
                        $string = $string.' completed" title="Completed';
                        break;
                    case "In Progress":
                        $string = $string.' progress" title="Awaiting Response';
                        break;
                    default:
                        $string = $string.'" title="Incomplete';
                }

                return $string.'"';
            }

            foreach ($this->records as $record) {
                $member = Member::getMember($record->steamid);

                if ($member) {
                    $app = Applications::getApplication($record->app_id);

                    $id = "In Progress";

                    $appStatus = "Incomplete";
                    if ($app) { $appStatus = (($app->accepted == 1) ? "Completed" : "In Progress"); } else {
                        $id = "Waiting for Application";
                    }
                    
                    $interviewStatus = (($record->interview_id != -1) ? "Completed" : "Incomplete");

                    $trainingStatus = "Incomplete";
                    $assessmentStatus = "Incomplete";

                    if ($member->candidate_id != $record->id) {
                        $id = "Archived";
                    } else {
                        if (($appStatus == "Completed" && $interviewStatus == "Completed" && $trainingStatus == "Completed"&& $assessmentStatus == "Completed") || $member->is_in_unit == 1) {
                            $id = "Completed";
                        }
                    }

                    ?>
                    <tr id="<?=$id;?>">
                        <td><?=date('d/m/Y', strtotime($record->insert_time));?></td>
                        <td><?=$member->steamName;?></td>
                        <td <?=printStatus($appStatus);?> style="width: 46px;"></td>
                        <td <?=printStatus($interviewStatus);?> style="width: 46px;"></td>
                        <td <?=printStatus();?> style="width: 46px;"></td>
                        <td <?=printStatus();?> style="width: 46px;"></td>
                        <td class = "manage button"><a href="<?=URL;?>unit/recruitment/<?=$record->id;?>">View</a></td>
                    </tr>
                    <?php
                }
            }
            ?>
        </table>
    </section>
    <script type="text/javascript">
        var activeButton = null;

        function filterApps(button, filter) {
            tr = document.getElementById("table").getElementsByTagName("tr");

            if (activeButton != null) {
                activeButton.classList.remove("active");
            }

            button.classList.add("active");
            activeButton = button;

            for (i = 0; i < tr.length; i++) {
                var id = tr[i].id;

                td = tr[i].getElementsByTagName("td")[0];
                if (td) {
                    if (id == filter) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                } 
            }
        }

        filterApps(document.getElementById("default"), "In Progress");
    </script>
    <?php
} else {
    ?>
    <section class = "container">
        <div class="profile-body">
            <div class="col">
                <div class = "steam-pfp-box no-border">
                    <img class = "steam-pfp" src="<?=$this->member->steampfplarge;?>" height="128" width="128">
                </div>
                <h3 class="name"><?=Member::getFriendlyName($this->member->first_name, $this->member->last_name, $this->member->steamName);?></h3>
                <?php
                if ($this->member->rank != -1) {
                    if ($this->member->assignment == -1) {
                        $assignment = ((object) array (
                            "name" => "Unassigned",
                            "orbat_name" => "Unassigned"
                        ));
                    } else {
                        $assignment = Assignments::getAssignment($this->member->assignment);
                    }
                    ?>
                    <h4><?=(Application::getRankInfo($this->member->rank, (($assignment) ? $assignment->name : "")))->name;?>, <?=(($assignment) ? $assignment->orbat_name : "In Training");?></h4>
                    <?php
                }
                ?>
                <div class = "tab-buttons vertical" style="margin-top: 10px;">
                    <?php 
                    foreach ($this->subpages as $subpage) {
                        echo '<a class="tab-button'.(($this->subpage == $subpage) ? " active" : "").'" href="'.URL.'unit/recruitment/'.$this->record->id.'/'.str_replace(" ", "-", strtolower($subpage)).'">'.$subpage.'</a>';
                    } 
                    ?>
                </div>
            </div>
            <div class="col main">
                <?php
                $path = "stages/".str_replace(" ", "-", strtolower($this->subpage)).".php";
                if (!file_exists($path)) {
                    include ($path);
                }
                ?>
            </div>
        </div>
    </section>
    <?php
}
?>