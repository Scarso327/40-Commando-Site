<?php

class Trainings {

    public static function trainingExists($training) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT * FROM trainings WHERE name = :training LIMIT 1");
        $query->execute(array(":training" => $training));

        return ($query->rowCount() > 0);
    }

    public static function getTrainings() {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT * FROM trainings");
        $query->execute();

        if ($query->rowCount() == 0) { return false; }
        return $query->fetchAll();
    }

    public static function getTraining($training) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT * FROM trainings WHERE id = :training OR `name` = :training");
        $query->execute(array(":training" => $training));

        if ($query->rowCount() == 0) { return false; }

        $return = $query->fetch();
        $return->content = self::getTrainingContent($return->id);
        return $return;
    }

    private static function getTrainingContent($training_id) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT * FROM training_content WHERE parent_id = -1 AND training_id = :training_id");
        $query->execute(array("training_id" => $training_id));

        if ($query->rowCount() == 0) { return false; }

        $return = array();

        foreach ($query->fetchAll() as $content) {
            $content->sections = self::getTrainingSections($content->id);

            array_push($return, $content);
        }
        
        return $return;
    }

    private static function getTrainingSections($section_id) {
        $query = Database::getFactory()->getConnection(DB_NAME)->prepare("SELECT * FROM training_content WHERE parent_id != -1 AND parent_id = :section_id");
        $query->execute(array(":section_id" => $section_id));

        if ($query->rowCount() == 0) { return array(); }
        return $query->fetchAll();
    }
}