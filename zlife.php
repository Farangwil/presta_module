<?php

if ( !defined('_PS_VERSION_'))
	exit;

class Zlife extends Module {
	
	public function __construct()
	{
		$this->name = 'zlife';
		$this->tab = 'front_office_features';
		$this->version = '1.0.0';
		$this->author = 'farang';
		$this->need_instance = 0;
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_); 
		$this->bootstrap = true;

		parent::__construct();

		$this->displayName = $this->l('ZLife - Construct a better world');
		$this->description = $this->l('Slave Woman retailer since 1876, Zlife Module is the best choice for a better world');

		$this->confirmUninstall = $this->l('Oh non mec ne fais pas ça ! Ta vie va changer ... ');

		if (!Configuration::get('ZLIFE_TITLE'))      
			$this->warning = $this->l('No name provided');
	}

	public function install()
	{
		if (Shop::isFeatureActive())
			Shop::setContext(Shop::CONTEXT_ALL);

		return parent::install() &&
		$this->registerHook('top') &&
		$this->registerHook('header') &&
		Configuration::updateValue('ZLIFE_TITLE', 'Zlife') &&
		Configuration::updateValue('ZLIFE_EDITO', 'Bienvenue sur votre prestashop, içi vous pouvez changer le contenu via le backoffice') &&
		Configuration::updateValue('ZLIFE_TEXT_LINK', 'Voir nos promotions');
	}

	public function uninstall()
	{
		if (!parent::uninstall() ||
			!Configuration::deleteByName('ZLIFE_TITLE')
			)
			return false;

		return true;
	}

	public function hookDisplayTop($params)
	{
		$this->context->smarty->assign(
			array(
				'zlife_title' => Configuration::get('ZLIFE_TITLE'),
				'zlife_edito' => Configuration::get('ZLIFE_EDITO'),
				'zlife_text_link' => Configuration::get('ZLIFE_TEXT_LINK'),
				'zlife_link' => $this->context->link->getModuleLink('zlife', 'test')
				)
			);
		return $this->display(__FILE__, 'zlife.tpl');
	}

	public function hookDisplayHeader()
	{
		$this->context->controller->addCSS($this->_path.'css/zlife.css', 'all');
	}  

	public function getContent()
	{
		$output = null;

		if (Tools::isSubmit('submit'.$this->name))
		{
			$my_module_name = strval(Tools::getValue('ZLIFE_TITLE'));
			$content = strval(Tools::getValue('ZLIFE_EDITO'));
			$text_link = strval(Tools::getValue('ZLIFE_TEXT_LINK'));
			if (!$my_module_name
				|| empty($my_module_name)
				|| !Validate::isGenericName($my_module_name))
				$output .= $this->displayError($this->l('Invalid Configuration value'));
			else
			{
				Configuration::updateValue('ZLIFE_TITLE', $my_module_name);
				Configuration::updateValue('ZLIFE_EDITO', $content);
				Configuration::updateValue('ZLIFE_TEXT_LINK', $text_link);
				$output .= $this->displayConfirmation($this->l('Settings updated'));
			}
		}
		return $output.$this->displayForm();

	}

	public function displayForm()
	{
    // Get default language
		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

    // Init Fields form array
		$fields_form[0]['form'] = array(
			'legend' => array(
				'title' => $this->l('Settings'),
				),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Titre'),
					'name' => 'ZLIFE_TITLE',
					'size' => 64,
					'required' => true
					),
				array(
					'type' => 'textarea',
					'label' => $this->l('Contenu edito'),
					'name' => 'ZLIFE_EDITO',
					'required' => true
					),
				array(
					'type' => 'text',
					'label' => $this->l('Texte du lien'),
					'name' => 'ZLIFE_TEXT_LINK',
					'required' => true
					),
				),
			'submit' => array(
				'title' => $this->l('Save'),
				'class' => 'button'
				)
			);

		$helper = new HelperForm();

    // Module, token and currentIndex
		$helper->module = $this;
		$helper->name_controller = $this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

    // Language
		$helper->default_form_language = $default_lang;
		$helper->allow_employee_form_lang = $default_lang;

    // Title and toolbar
		$helper->title = $this->displayName;
	    $helper->show_toolbar = true;        // false -> remove toolbar
	    $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
	    $helper->submit_action = 'submit'.$this->name;
	    $helper->toolbar_btn = array(
	    	'save' =>
	    	array(
	    		'desc' => $this->l('Save'),
	    		'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
	    		'&token='.Tools::getAdminTokenLite('AdminModules'),
	    		),
	    	'back' => array(
	    		'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
	    		'desc' => $this->l('Back to list')
	    		)
	    	);

    // Load current value
	    $helper->fields_value['ZLIFE_TITLE'] = Configuration::get('ZLIFE_TITLE');
	    $helper->fields_value['ZLIFE_EDITO'] = Configuration::get('ZLIFE_EDITO');
	    $helper->fields_value['ZLIFE_TEXT_LINK'] = Configuration::get('ZLIFE_TEXT_LINK');

	    return $helper->generateForm($fields_form);
	}

}
