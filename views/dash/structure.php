<!--- TODO: DESCRIPTION --->
<?php
foreach ($this->ranks as $value=>$ranks) {
    ?>
    <section class="container">
        <h2><?=$value;?></h2>
        <table>
            <tr class="header">
                <th>Insignia</th>
                <th>Name</th>
                <th>Abbr</th>
                <th>Description</th>
            </tr>
            <?php
            foreach ($ranks as $key=>$rank) {
                ?>
                <tr id="<?=($key + 1)."-".$rank->short_name;?>">
                    <td><img src="<?=URL?>images/rank-insignia/<?=$rank->image;?>" alt="rank-insig" height="64px" width="64px"/></td>
                    <td><?=$rank->name?></td>
                    <td><?=$rank->short_name;?></td>
                    <td><?=$rank->description;?></td>
                </tr>
                <?php
            }
            ?>
        </table>
    </section>
    <?php
}
?>
<p style="text-align: center; opacity: 0.7; margin-bottom: 35px;">Some ranks may not be 100% realistic. We have appropriated certain ranks to ensure we maintain a sense of progression where there might not be in real life.</p>