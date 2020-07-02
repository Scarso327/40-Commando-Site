<?php

/*
**  Protected Form Field Names:
**      - steamid : Indicates the member by steamid id e're submitting this for...
*/

/*
**  Rule On Form Custom Functions:
**      - fields : each field is then passed as a parameter in order of their apperance, even if it's not used...
*/

class Forms {

    public static $steamidOverride = ""; // Overrides any input named "steamid"...
    private static $fieldPrefix = "";
    private static $member = false;
    public static $customLogData = array();

    public function buildForm ($form, $fields, $url = "", $readonly = false) {
        if (self::$steamidOverride != "") {
            self::$member = Member::getMember(self::$steamidOverride);

            if (!self::$member) {
                new DisplayError("#Fe006", true);
                exit;
            }
        }

        if ($url == "") {
            $url = URL."unit/form/".$form->id."-".str_replace(' ', '', $form->name)."/submitted/";
        }

        echo "
        <small>".$form->description."</small></br>
        <form autocomplete='off' method='".$form->method."' action='".$url."'>
            <input type='hidden' name = '_action'>
            ";

            foreach ($fields as $field) {
                self::$fieldPrefix = ""; // Reset...

                if ($readonly) { 
                    self::$fieldPrefix = " readonly"; 
                } else {
                    if ($field->required == 1) { self::$fieldPrefix = " required"; }
                }

                if ($field->hidden == 0) {
                    echo "
                    <div class='form-group'>
                        <b><label for='".$field->fieldName."'>".$field->name.":</label></b>";
                        self::{$field->type}($field, $readonly);
                        echo "
                        <small>".$field->description."</small>
                    </div>";
                } else {
                    self::{$field->type}($field, $readonly);
                }
            }

            echo "
            <div class='form-group'>
                <button type='submit'>Submit Form</button>
            </div>
        </form>
        ";
    }

    public function onFormSubmit ($form, $fields) {
        if (!Form::canSubmitForm($form)) {
            new DisplayError("#Fe011");
            exit;
        }

        $return = $form->return;
        $fieldsArr = array();

        foreach ($fields as $field) {
            $value = $_POST[$field->fieldName];

            switch (true) {
                case (!array_key_exists($field->fieldName, $_POST) && $field->required == 1):
                    new DisplayError("#Fe006");
                    exit;
                case ($value == "" && $field->required == 1):
                    new DisplayError("#Fe007");
                    exit;
                case ($value == "" && $field->required == 0):
                    $value = $field->default;
                    break;
            }

            Filter::XSSFilter($value);
            Filter::clearAll($value);

            $fieldsArr[$field->fieldName] = array(
                "name" => $field->name, 
                "fieldName" => $field->fieldName, 
                "value" => $value,
                "if_blank" => $field->if_blank,
                "is_hidden" => ($field->hidden == 1 || $field->hide_from_log == 1)
            );

            $return = str_replace("{{".$field->fieldName."}}", $value, $return);
        }
        
        $actions = new Actions;

        if ($form->customFunction != "") {
            $params = array();

            foreach ($fieldsArr as $field) {
                array_push($params, $field["value"]);
            }

            if (method_exists($actions, $form->customFunction)) {
                $ret = call_user_func_array(array($actions, $form->customFunction), $params);
                
                if (!$ret) {
                    new DisplayError("#500");
                    exit;
                }

                if (is_array($ret)) {
                    foreach ($ret as $field) {
                        $fieldsArr[$field->fieldName] = array(
                            "name" => $field->name, 
                            "fieldName" => $field->fieldName, 
                            "value" => $field->value,
                            "if_blank" => $field->if_blank,
                            "is_hidden" => false
                        );
                    }
                }
            }
        }

        if ($form->submitLog == 1) {
            $logid = Logs::log($fieldsArr, Account::$steamid, $form->action, $form->status, 0);
            if (!$logid) {
                new DisplayError("#500");
                exit;
            }

            if ($form->logFunction != "" && method_exists($actions, $form->logFunction) && array_key_exists("steamid", $fieldsArr)) {
                call_user_func_array(array($actions, $form->logFunction), array(
                    ((array_key_exists("steamid", $fieldsArr)) ? $fieldsArr["steamid"]["value"] : ""), 
                    $logid
                ));
            }
        }

        Header("Location: ".URL.$return);
    }

    // Different Fields...

    /* Generic Inputs */

    private static function Input ($field, $readonly) {
        $value = "";
        $type = "";

        if ($field->fieldName == "steamid") {
            $value = self::$steamidOverride;
        } else {
            if ($field->default != "") {
                $value = $field->default;
            }
        }

        if ($field->hidden == 1) { $type = "hidden"; }

        echo "<input type='".$type."' name = '".$field->fieldName."' value = '".$value."' ".$field->conditions.self::$fieldPrefix.">";
    }

    private static function Textarea ($field, $readonly) {
        echo "<textarea rows='5' cols='60' name = '".$field->fieldName."' ".$field->conditions.self::$fieldPrefix."></textarea>";
    }

    private static function Date($field, $readonly) {
        echo "<input type='date' name = '".$field->fieldName."' value = '".date('Y-m-d')."' min = '".date('Y-m-d')."' max='2025-12-12'>";
    }

    private static function Time($field, $readonly) {
        echo "<input type='time' name = '".$field->fieldName."' value = '12:00'>";
    }

    private static function ConfirmCheckbox($field, $readonly) {
        echo "
            <div class='radio'><span><input type='radio' name = '".$field->fieldName."' value='Yes' ".$field->conditions.self::$fieldPrefix."> Yes</span>
            <span><input type='radio' name = '".$field->fieldName."' value='No' checked ".$field->conditions.self::$fieldPrefix."> No</span></div>
        ";
    }

    private static function Awards($field, $readonly) {
        $items = array();

        foreach (Trainings::getTrainings() as $training) {
            array_push($items, ((object) array(
                "name" => $training->name,
                "value" => $training->name
            )));
        }

        self::Dropdown($field, $items);
    }

    private static function Ranks($field, $readonly) {
        $items = array();

        foreach (Application::$ranks as $rank) {
            array_push($items, ((object) array(
                "name" => $rank->info["default"]->name,
                "value" => $rank->id
            )));
        }

        self::Dropdown($field, $items);
    }

    private static function createAssignments($assignments, $prefix = "") {
        
        foreach ($assignments as $assignment) {
            echo '<option value="'.$assignment['id'].'" '.((self::$steamidOverride != "" && (self::$member->assignment == $assignment['id'])) ? "selected=\"selected\"" : "").'>'.$prefix.$assignment['name'].'</option>';
            self::createAssignments($assignment['sub_units'], $prefix." - ");
        }
    }

    private static function Assignments($field, $readonly) {
        echo "<select name = '".$field->fieldName."' ".$field->conditions.self::$fieldPrefix.">";
            foreach (Assignments::getCompanies() as $company) {
                self::createAssignments(array($company));
            }
        echo "</select>";
    }

    private static function createAssignmentCheckboxs($field, $assignments, $prefix = "") {
        foreach ($assignments as $assignment) {
            if ($assignment['name'] != "Unassigned") {
                echo '<span><input type="checkbox" name = "'.$field->fieldName.'[]" value="'.$assignment['id'].'" '.((in_array($assignment['id'], self::$member->sec_assignment)) ? "checked" : "").''.$field->conditions.self::$fieldPrefix.'> '.$prefix.$assignment['name'].'</span>';
                self::createAssignmentCheckboxs($field, $assignment['sub_units'], $prefix." - ");
            }
        }
    }

    private static function AssignmentCheckboxs($field, $readonly) {
        if (!self::$member) {
            new DisplayError("#500", true);
            exit;
        }
        ?>
        <div class='radio'>
            <?php
            foreach (Assignments::getCompanies() as $company) {
                self::createAssignmentCheckboxs($field, array($company));
            }
            ?>
        </div>
        <?php
    }

    private static function Weather($field, $readonly) {
        $items = array();

        foreach (array("Clear Skies") as $weather) {
            array_push($items, ((object) array(
                "name" => $weather,
                "value" => $weather
            )));
        }

        self::Dropdown($field, $items);
    }

    private static function Dropdown($field, $items) {
        echo "<select name = '".$field->fieldName."' ".$field->conditions.self::$fieldPrefix.">";
            foreach ($items as $item) {
                echo '<option value="'.$item->value.'">'.$item->name.'</option>';
            }
        echo "</select>";
    }
}