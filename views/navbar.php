<nav class="main-nav<?php if (isset($this->nav_fixed)) { ?> fixed<?php } ?>">
    <nav class="main container">
        <ul class="buttons">
            <li class="site-logo"><img src="<?=URL?>images/logo.png" alt="site-logo" height="60px"></li>
            <li class="site-title"><a>40 Commando, A Coy</a></li>
            <div class="force-right">
                <li <?php if (View::ButtonActive("Home")) { echo 'class = "active"'; } ?>>
                    <a href="<?=URL?>">Home</a>
                </li>
                <li <?php if (View::ButtonActive("Ranks & Positions")) { echo 'class = "active"'; } ?>>
                    <a href="<?=URL?>structure/">Ranks & Positions</a>
                </li>
                <li>
                    <a href="https://steamcommunity.com/sharedfiles/filedetails/?id=2091853497" target="_blank">Modpack</a>
                </li>
                <li <?php if (View::ButtonActive("ORBAT")) { echo 'class = "active"'; } ?>>
                    <a href="<?=URL?>orbat/">Order of Battle</a>
                </li>
                <li class="sep"><a>|</a></li>
                <?php
                if (Account::isLoggedIn()) {
                    ?>
                        <li <?php if (View::ButtonActive("Unit Access") || View::ButtonActive("Application")) { echo 'class = "active"'; } ?>>
                            <a href="<?=URL.((!Account::$member->is_in_unit) ? "app" : "unit/personnel/".Account::$steamid);?>">
                                <img class = "steam-pfp" src="<?=Session::get("steaminfo")["steam-pfp"];?>" alt="PFP" height="32" width="32">
                            </a>
                        </li>
                        <li title="Logout">
                            <a href="<?=URL.'?logout'?>">
                                <span class="fas fa-sign-out-alt"></span> 
                            </a>
                        </li>
                    <?php
                } else {
                    ?>
                    <li <?php if (View::ButtonActive("Login")) {  echo 'class = "active"'; } ?> title="Login">
                        <a href="<?=URL.'login';?>"><span class="fas fa-sign-in-alt"></span></a>
                    </li>
                    <?php
                }
                ?>
                <li title="<?=((Application::$isDark) ? "Light" : "Dark");?> Theme">
                    <a theme-toggle class="theme-toggle"><span class="fas <?=((Application::$isDark) ? "fa-sun" : "fa-moon");?>"></span></a>
                </li>
            </div>
        </ul>
    </nav>
    <?php 
    switch (true) {
        case (View::ButtonActive("Application")):
            ?>
            <nav class="sub">
                <div class="container">
                    <ul class="buttons">
                        <li <?php if (View::ButtonActive("Application")) { echo 'class = "active"'; } ?>>
                            <a href="<?=URL;?>app/">Application Progress</a>
                        </li>
                    </ul>
                </div>
            </nav>
            <?php
            break;
        case (View::ButtonActive("Unit Access")):
            ?>
            <nav class="sub">
                <div class="container">
                    <ul class="buttons">
                        <li <?php if (View::ButtonActive("Personnel")) { echo 'class = "active"'; } ?>>
                            <a href="<?=URL;?>unit/personnel/">Personnel</a>
                        </li>
                        <li <?php if (View::ButtonActive("Operations")) { echo 'class = "active"'; } ?>>
                            <a href="<?=URL;?>unit/operations/">Operations</a>
                        </li>
                        <li <?php if (View::ButtonActive("Trainings")) { echo 'class = "active"'; } ?>>
                            <a href="<?=URL;?>unit/trainings/">Trainings</a>
                        </li>
                        <li <?php if (View::ButtonActive("Recruitment")) { echo 'class = "active"'; } ?>>
                            <a href="<?=URL;?>unit/recruitment/">Recruitment</a>
                        </li>
                        <li <?php if (View::ButtonActive("Statistics")) { echo 'class = "active"'; } ?>>
                            <a href="<?=URL;?>unit/statistics/">Statistics</a>
                        </li>
                    </ul>
                </div>
            </nav>
            <?php
            break;
    }
    ?>
</nav>