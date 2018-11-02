<?php

namespace application\models\Level;


class LevelUtil
{
    public static function getLevels()
    {
        return Level::find()->orderBy(['name' => SORT_ASC])->all();
    }

    public static function getDropdownList($isRequired = true)
    {
        $results = array();
        if (!$isRequired) {
            $results[''] = '--Chọn--';
        }
        foreach (LevelUtil::getLevels() as $index => $level) {
            $results[$level->id] = $level->name;
        }
        return $results;
    }
}
