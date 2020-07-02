<header class="banner<?php if (isset($this->fulllength)) { ?> title<?php } ?>" style="background-image: url('<?=URL;?>images/banner.jpg');">
    <div class="overlay"></div>
    <?php
    if (isset($this->title)) {
        ?>
        <div class="center">
            <h1><?=$this->title;?></h1>
        </div>
        <?php
    }
    ?>
</header>