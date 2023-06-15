<?php

require_once('BaseRepository.php');

class SettingsRepository extends BaseRepository
{
    protected $tableName = "settings";

    public function __construct()
    {
        parent::__construct();
    }

    public function createSetting(string $name, $value) {
        $settings = ORM::for_table(self::getTableName())
            ->create();
        $settings->name = $name;
        if (is_array($value)) {
            $value = json_encode($value);
        }
        $settings->value = $value;
        return $settings->save();
    }

    public function updateSettingBy(string $field, $fieldValue, $value) {
        $settings = ORM::for_table(self::getTableName())
            ->where($field, $fieldValue)
            ->findOne();
        if (!$settings instanceof ORM) {
            return false;
        }
        if (is_array($value)) {
            $value = json_encode($value);
        }
        $settings->value = $value;
        return $settings->save();

    }

    public function updateSettingByName(string $name, $value) {
        return $this->updateSettingBy('name', $name, $value);
    }

    public function updateSettingById($id, $value) {
        return $this->updateSettingBy('id', $id, $value);
    }

    public function fetchSettingBy(string $field, $fieldValue) {
        return ORM::for_table(self::getTableName())
            ->where($field, $fieldValue)
            ->findOne();
    }
}