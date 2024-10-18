<?php
/**
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    Massimiliano Palermo <info@mpsoft.it>
*  @copyright 2007-2018 Digital Solutions® Massimiliano Palermo
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__) . '/helpers/LoadClass.php';

class MpSizeChart extends Module
{
    protected $config_form = false;
    protected $adminClassName = 'AdminMpSizeChart';

    protected $upload_dir;

    public $id_lang;
    public $id_shop;
    public $tablename;

    public function __construct()
    {
        $this->name = 'mpsizechart';
        $this->tab = 'front-office-features';
        $this->version = '1.3.1';
        $this->author = 'massimiliano Palermo';
        $this->need_instance = 0;
        $this->module_key = '';

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        (new LoadClass($this))->load('MpSizeChartModelAttachments', 'models');
        (new LoadClass($this))->load('MpSizeChartGetAttachment', 'helpers');
        (new LoadClass($this))->load('MpSizeChartInstallMenu', 'helpers');
        (new LoadClass($this))->load('MpSizeChartInstallTable', 'helpers');

        $this->displayName = $this->l('MP Taglie');
        $this->description = $this->l('Con questo modulo puoi visualizzare le informazioni sulle taglie tramite PDF.');
        $this->confirmUninstall = $this->l('Are you sure you want uninstall this module?');
        $this->ps_versions_compliancy = ['min' => '1.6', 'max' => _PS_VERSION_];
        $this->upload_dir = Tools::getShopProtocol() . Tools::getHttpHost() . __PS_BASE_URI__ . 'upload/mpsizechart/';
        $this->id_lang = (int) Context::getContext()->language->id;
        $this->id_shop = (int) Context::getContext()->shop->id;
    }

    public function install()
    {
        $installMenu = new MpSizeChartInstallMenu($this);
        $installtable = new MpSizeChartInstallTable($this);

        return parent::install()
            && $this->registerHook('displayAfterDescriptionShort')
            && $this->registerHook('actionAdminControllerSetMedia')
            && $this->registerHook('actionFrontControllerSetMedia')
            && $installMenu->installMenu($this->l('MP Taglia'), $this->adminClassName, 'AdminCatalog')
            && $installtable->installFromSqlFile('product_size_chart_attachments');
    }

    public function uninstall()
    {
        $installMenu = new MpSizeChartInstallMenu($this);

        return parent::uninstall()
            && $installMenu->uninstallMenu($this->adminClassName);
    }

    public function installSQL()
    {
        $file = $this->getLocalPath() . 'sql/product_size_chart_attachments.sql';
        if (!file_exists($file)) {
            // Non ho trovato il file... continuo lo stesso con l'installazione
            return true;
        }

        $sql = file_get_contents($file);
        $sql = str_replace('{PFX}', _DB_PREFIX_, $sql);

        // Provo a creare la tabella
        return Db::getInstance()->execute($sql);
    }

    public function hookActionAdminControllerSetMedia()
    {
        $this->context->controller->addCSS($this->getLocalPath() . 'views/css/icon-menu.css');
    }

    public function hookActionFrontControllerSetMedia()
    {
        $this->context->controller->addCSS($this->getLocalPath() . 'views/css/button-style.css');
    }

    public function hookDisplayAfterDescriptionShort($params)
    {
        return $this->hookDispatch($params);
    }

    public function hookDispatch($params)
    {
        $id_product = (int) $params['product']->id;
        $filename_url = MpSizeChartGetAttachment::getAttachmentUrl($id_product);
        $filename = basename($filename_url);

        $file_location = _PS_UPLOAD_DIR_ . 'mpsizechart/' . $filename;
        if (!file_exists($file_location)) {
            return;
        }

        $tpl_file = $this->getLocalPath() . '/views/templates/front/displayButton.tpl';
        $tpl = $this->context->smarty->createTemplate($tpl_file);
        $tpl->assign(
            [
                'id_product' => $id_product,
                'url' => $filename_url,
            ]
        );

        return $tpl->fetch();
    }
}