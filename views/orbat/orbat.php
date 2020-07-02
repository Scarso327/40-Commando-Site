<?php
if (isset($this->orbat_search)) {
    ?>
    <section class = "container form">
        <input id="orbat-search" type="text" onkeyup="Filter()" placeholder="Search (Name, Steam ID)">
    </section>
    <?php
}
?>
<div class="orbats">
    <?php
    function printMembers($members, $assignment) {
        if (count($members) > 0) {
            ?>
            <div class="members">
            <?php
            usort($members, function ($member1, $member2) {
                if ($member1->rank == $member2->rank) {
                    return $member1->rank_change_date > $member2->rank_change_date;
                } else {
                    return $member1->rank < $member2->rank;
                }
            });

            foreach($members as $member) {
                if ($member->is_in_unit == 1) { 
                    ?>
                    <a date-name="<?=$member->first_name;?> <?=$member->last_name;?>" date-steamid="<?=$member->steamid;?>" href="<?=URL;?>unit/personnel/<?=$member->steamid;?>"><?=Application::buildName(
                        (Application::getRankInfo($member->rank, $assignment))->short_name, 
                        $member->first_name, $member->last_name, ($member->is_acting == 1));?></a>
                    <?php
                }
            }
            ?>
            </div>
            <?php
        }
    }

    function printAssignments($assignments) {
        if (count($assignments) > 0) {
            ?>
            <ul>
                <?php
                foreach ($assignments as $assignment) {
                    $members = Assignments::getAssignmentMember($assignment['id']);
                    ?>
                    <li>
                        <div class="branch">
                            <img src="<?=URL;?>images/nato-symbols/<?=$assignment['icon'];?>.png">
                            <?=$assignment['orbat_name'];?>
                            <?=printMembers($members, $assignment['name']);?>
                        </div>
                        <?php
                        if (isset($assignment['sub_units'])) {
                            printAssignments($assignment['sub_units']);
                        }
                        ?>
                    </li>
                    <?php
                }
                ?>
            </ul>
            <?php
        }
    }

    foreach ($this->companies as $company) {
        $members = Assignments::getAssignmentMember($company['id']);
        ?>
        <section class="container tree">
            <h2><?=$company['name'];?></h2>
            <ul>
                <li>
                    <div class="branch">
                        <img src="<?=URL;?>images/nato-symbols/<?=$company['icon'];?>.png">
                        <?=$company['orbat_name'];?>
                        <?=printMembers($members, $company['name']);?>
                    </div>
                    <?=printAssignments($company['sub_units']);?>
                </li>
            </ul>
        </section>
        <?php
    }
    ?>
</div>
<?php
if (isset($this->orbat_search)) {
    ?>
    <script type="text/javascript">
        var input = document.getElementById("orbat-search");
        var orbat = document.getElementsByClassName("orbats")[0];
        var personel = orbat.getElementsByTagName("a");

        function Filter() {
            var search = input.value.toUpperCase();

            for (i = 0; i < personel.length; i++) {
                var person = personel[i];

                if (
                    ((person.getAttribute("date-name").toUpperCase()).indexOf(search))> -1 || 
                    ((person.getAttribute("date-steamid").toUpperCase()).indexOf(search)) > -1
                ) {
                    person.style.display = "";
                } else {
                    person.style.display = "none";
                }
            }
        }
    </script>
    <?php
}
?>