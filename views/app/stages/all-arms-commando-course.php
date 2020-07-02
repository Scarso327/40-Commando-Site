<section class="container">
    <?php
    $training = Trainings::getTraining(1);
    if (!$training) {
        new DisplayError("#500", true);
        exit;
    }

    $assignment = Assignments::getAssignment($this->member->assignment);

    if (!$assignment) {
        $assignment = "Unassigned";
    } else {
        $assignment = $assignment->name;
    }
    ?>
    <h2 class="accent-text"><b>YOU'RE IN!</b></h2>
    <p>You have successfully completed your Application and Interview making you apart of 40 Commando, A Coy. This means you're now a <a target="_blank" href="<?=URL;?>structure/#1-rec">Recruit</a> and have been given a temporary assignment of <?=$assignment;?>. Once you've completed your AACC and Assessment you'll be granted the rank of <a target="_blank" href="<?=URL;?>structure/#2-mne">Marine</a> and be given a more permanent assignment.</p>
    <h2>The All Arms Commando Course</h2>
    <p>This is the next stage of your recruitment process, it can be completed at any time but not completing this will limit your options within our unit. Below has a few scheduled trainings of this type that you can mark yourself as "Going" or "Not Going". On top of this you can view the training's content divided up into each specific section.</p></br>
    <div class="flex-boxes">
        <div class="flex-box bigger" style="max-width: 60%; margin: 0; margin-right: 50px;">
            <h3>
                <span class="accent-text">Lesson</span> Content
                <?php
                if ($training->content) {
                    ?>
                    <select class="in-title-dropdown" onchange="onLessonChange(this.value)">
                        <?php
                        foreach ($training->content as $content) {
                            echo '<option value="'.$content->name.'">'.$content->name.'</option>';
                        }
                        ?>
                    </select>
                    <?php
                }
                ?>
            </h3>
            <?php
            if ($training->content) {
                ?>
                <table id="table" class="centred">
                    <tr class="header">
                        <th>Stage</th>
                        <th>Method</th>
                        <th>Stage Content</th>
                    </tr>
                    <?php
                    foreach ($training->content as $c_key=>$content) {
                        foreach ($content->sections as $s_key=>$section) {
                            ?>
                            <tr id="<?=$content->name;?>">
                                <td><?=($c_key + 1).".".($s_key + 1);?></td>
                                <td><?=$section->method;?></td>
                                <td><?=$section->name;?></td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                </table>
                <script type="text/javascript">
                    tr = document.getElementById("table").getElementsByTagName("tr");

                    function onLessonChange(value) {
                        for (i = 0; i < tr.length; i++) {
                            var id = tr[i].id;

                            td = tr[i].getElementsByTagName("td")[0];
                            if (td) {
                                if (id == value) {
                                    tr[i].style.display = "";
                                } else {
                                    tr[i].style.display = "none";
                                }
                            } 
                        }
                    }

                    onLessonChange("<?=$training->content[0]->name;?>");
                </script>
                <?php
            } else {
                echo '<p style="text-align: center; opacity: 0.7;">No Content<p>';
            }
            ?>
        </div>
        <div class="flex-box" style="margin: 0;">
            <h3><span class="accent-text">Scheduled</span> Trainings</h3>
            <p style="text-align: center; opacity: 0.7;">Nothing Scheduled<p>
        </div>
    </div>
</section>