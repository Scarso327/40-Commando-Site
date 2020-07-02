<?php
if (!isset($this->record)) {
    ?>
    <div class="body">
        <section class="container further">
            <h2 class="title">WE'RE <span class="accent-text">RECRUITING</span></h2>
            <h2 class="subtitle">Miniumum Requirements</h2>
            <div class="flex-boxes" style="margin: 50px 0;">
                <div class="flex-box">
                    <h2 class="icon-title"><i class="fas fa-shopping-cart"></i></h2>
                    <h2 class="subtitle">Own ArmA 3</h2>
                </div>
                <div class="flex-box">
                    <h2 class="icon-title"><i class="fas fa-birthday-cake"></i></h2>
                    <h2 class="subtitle">Age 13 or Parental Consent</h2>
                </div>
                <div class="flex-box">
                    <h2 class="icon-title"><i class="fas fa-language"></i></h2>
                    <h2 class="subtitle">Speak Fluent English</h2>
                </div>
                <div class="flex-box">
                    <h2 class="icon-title"><i class="fas fa-percentage"></i></h2>
                    <h2 class="subtitle">Maintain 60% Attendance Minimum</h2>
                </div>
            </div>
            <div class="center"><a class="standalone-button big" href="?start-process">Start Application Process</a></div>
        </section>
    </div>
    <?php
} else {
    ?>
    <section class = "container">
        <div class = "tab-buttons">
            <?php
            $hasAccess = false;

            foreach (array("Register Account", "Application", "Interview", "All Arms Commando Course", "Assessment") as $button) {
                $isActive = false;
                $name = (is_array($button)) ? $button[0] : $button;
                $stage = (is_array($button)) ? $button[1] : $button;
                $classes = "tab-button";
                
                if ($this->page == ucfirst(strtolower($stage))) { $classes = $classes." active"; $isActive = true; }
                if (in_array($name, $this->completed)) {
                    $classes = $classes." completed";

                    if ($isActive) { $hasAccess = true; }
                } else {
                    if (strtolower($button) == $this->current) {
                        $classes = $classes." current";

                        if ($isActive) { $hasAccess = true; }
                    }
                }

                echo '<a class="'.$classes.'" href="'.URL.'app/?stage='.(strtolower ($stage)).'">'.$name.'</a>';
            }
            ?>
        </div>
    </section>
    <?php
    if ($hasAccess) {
        $path = "stages/".str_replace(" ", "-", strtolower($this->page)).".php";
        if (!file_exists($path)) {
            include ($path);
        }
    } else {
        ?>
        <section class="container">
            <h2>You haven't reached this stage yet!</h2>
            <p>You must complete all previous stages before you can begin this one.</p>
        </section>
        <?php
    }
}
?>