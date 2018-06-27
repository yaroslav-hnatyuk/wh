<?php

namespace App\Services;
use App\Entities\Rating;

class SettingsService extends BaseService
{

    public function getOneByName($name)
    {
        return $this->db->fetchAssoc("SELECT * FROM `settings` WHERE `name`=?", [$name]);
    }

    public function getAll()
    {
        return $this->db->fetchAll("SELECT * FROM `settings`");
    }

    function saveSettings($settings = array())
    {
        $this->db->beginTransaction();
            try {
            foreach ($settings as $setting) {
                if ($this->getOneByName($setting['name'])) {
                    $this->db->update('`settings`', ['value' => $setting['value']], ['name' => $setting['name']]);
                }
            }
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }

        return $settings;
    }

}
