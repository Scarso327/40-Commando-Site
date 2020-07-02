<?php
if (!Account::isLoggedIn()) {
    ?>
    <section class="banner" style="background-image: url('<?=URL;?>images/recruitment.png');">
        <div class="overlay"></div>
    </section>
    <?php
}
?>
<footer>
    <div class="container">
        <div class="social">
            <div class="media-list">
                <a href="https://discord.gg/zGB9xsg" target="_blank" title="Discord"><span class="fab fa-discord"></span></a>
                <a href="ts3server://40commandos.co.uk/" target="_blank" title="TeamSpeak"><span class="fab fa-teamspeak"></span></a>
                <a href="https://steamcommunity.com/groups/40-Commando" target="_blank" title="Steam"><span class="fab fa-steam"></span></a>
                <a href="https://units.arma3.com/unit/40-commandos" target="_blank" title="ArmA 3 Units"><span class="fas fa-shield-alt"></span></a>
            </div>
        </div>
        <div class="copyright">
            © 2020 40 Commando, A Coy. All rights reserved.<span>Website Designed & Developed by <a href="https://scarso.dev/" target="_blank">Scarso</a></span>
        </div>
    </div>
</footer>