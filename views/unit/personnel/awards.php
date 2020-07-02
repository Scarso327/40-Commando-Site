<div class = "form">
    <?=Forms::buildForm($this->form, $this->fields);?>
</div>
<table id = "myFormsTable">
    <tr class = "first">
        <th COLSPAN = 3>Details</th>
        <th>Actions</th>
    </tr>
    <tr class = "second">   
        <!--- Details --->
        <th>Badge</th>
        <th>Name</th>
        <th>Awarded</th>
        <th></th>
    </tr>
    <?php
    if ($this->awards) {
        foreach($this->awards as $award) {
            $badge = Trainings::getTraining($award->award);
            ?>
            <tr>
                <td style="display: flex; justify-content: center;"><div class="badge"><?=$badge->short_name;?></div></td>
                <td><?=$award->award;?></td>
                <td><?=date("d/m/Y", strtotime($award->date_awarded))?></td>
                <td class="table-buttons">
                    <a href="?remove=<?=$award->award;?>"><i class="fas fa-times-circle"></i></a>
                </td>
            </tr>
            <?php
        }
    } else {
        ?>
        <tr>
            <td COLSPAN = 5>No Awards</td>
        </tr>
        <?php
    }
    ?>
</table>