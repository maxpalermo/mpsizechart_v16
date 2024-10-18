<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    Massimiliano Palermo <maxx.palermo@gmail.com>
 * @copyright Since 2016 Massimiliano Palermo
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class MpSizeChartInstallMenu
{
    protected $module;

    public function __construct($module)
    {
        $this->module = $module;
    }

    public function installMenu($name, $controller_name, $tab_parent = null, $position = null)
    {
        if (!$name) {
            return false;
        }

        if (!$controller_name) {
            return false;
        }

        if (!is_array($name)) {
            $name = [];
            foreach (Language::getLanguages() as $lang) {
                $name[$lang['id_lang']] = $name;
            }
        } else {
            foreach (Language::getLanguages() as $lang) {
                if (!isset($name[$lang['id_lang']])) {
                    $name[$lang['id_lang']] = '--';
                }
            }
        }

        if ($tab_parent === null) {
            $id_tab_parent = -1;
        } else {
            $id_tab_parent = (int) Tab::getIdFromClassName($tab_parent);
        }

        if ($position === null) {
            $position = (int) Tab::getNewLastPosition($id_tab_parent);
        }

        $tab = new Tab();
        $tab->id_parent = $id_tab_parent;
        $tab->class_name = $controller_name;
        $tab->module = $this->module->name;
        $tab->position = (int) $position;
        $tab->active = 1;
        $tab->hide_host_mode = 0;
        // Multilang fields
        $tab->name = [];
        foreach (Language::getLanguages() as $lang) {
            $id_lang = (int) $lang['id_lang'];
            $tab->name[$id_lang] = $name[$id_lang];
        }

        return $tab->add();
    }

    public function uninstallMenu($controller_name)
    {
        $id_tab = (int) Tab::getIdFromClassName($controller_name);
        if ($id_tab) {
            $tab = new Tab((int) $id_tab);

            return $tab->delete();
        }

        return true;
    }
}