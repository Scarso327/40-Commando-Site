<section class = "login">
    <div class = "loginBox">
        <div class = "body">
            <h2>Login</h2>
            <form method="GET" action="<?=URL?>login/">
                <?php
                if (Account::isLoggedIn()) {
                    ?>
                    <div class = "notify">Already Logged In</div>
                    <?php
                } else {
                    ?>
                    <input type="hidden" name="_action" value="login">

                    <?php
                    if (isset($this->reason)) {
                        ?>
                        <div class = "notify highlighted-box" style="border: 1px solid #ECECEC; background-color: #f6f6f6;"><?php if($this->reason != "") {echo $this->reason; }?></div>
                        <?php
                    }
                    ?>

                    <button type="submit">
                        <!--- <span class="fas fa-steam"></span>  Disabled Because it didn't work... --->
                        <span>Login with Steam</span>              
                    </button>
                    <?php
                }
                ?>
            </form>
            <a href="<?=URL;?>">Return Home</a>
        </div>
    </div>
</section>