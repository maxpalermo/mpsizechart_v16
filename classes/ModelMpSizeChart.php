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
class ModelMpSizeChart extends ObjectModel
{
    public $id_product;
    public $filename;

    public static $definition = [
        'table' => 'mp_size_chart',
        'primary' => 'id_product',
        'fields' => [
            'filename' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true, 'size' => 255],
        ],
    ];

    public static function getFilenameByIdProduct($id_product)
    {
        $model = new self($id_product);
        if (!Validate::isLoadedObject($model)) {
            return false;
        }
        $filename = $model->filename;
        $path = _PS_MODULE_DIR_ . 'mpsizechart/upload/' . $filename;

        return file_exists($path) ? $path : false;
    }

    public static function getList()
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from(self::$definition['table']);

        return Db::getInstance()->executeS($sql);
    }
}
