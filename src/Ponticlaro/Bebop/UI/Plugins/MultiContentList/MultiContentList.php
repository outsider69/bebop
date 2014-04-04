<?php

namespace Ponticlaro\Bebop\UI\Plugins;

use Ponticlaro\Bebop;

class MultiContentList extends \Ponticlaro\Bebop\UI\PluginAbstract {

	/**
	 * Identifier Key to call this plugin
	 * 
	 * @var string
	 */
	protected static $__key = 'MultiList';

	/**
	 * Contains the URL for the directory containing this file
	 * 
	 * @var String
	 */
	protected static $__base_url;

	/**
	 * Holds configuration values
	 * 
	 * @var Ponticlaro\Bebop\Common\Collection
	 */
	protected $__config;

	/**
	 * Holds all added lists
	 * 
	 * @var Ponticlaro\Bebop\Common\Collection
	 */
	protected $__lists;

	/**
	 * Loads plugin OR creates single instance of the MultiContentList plugin
	 * 
	 */
	public function __construct()
	{
		// Get URL for the directory containing this plugin
		self::$__base_url = Bebop::getPathUrl(__DIR__);

		// Instantiate configuration collections
		$this->__config = Bebop::Collection();
		$this->__lists  = Bebop::Collection();

		// Get function arguments
		$args = func_get_args();

		// Conditionally creates single instance of the MultiContentList plugin
		if ($args) call_user_func_array(array($this, '__createInstance'), $args);
	}

	/**
	 * This function will register everything on the right hooks
	 * when the plugin is added to Bebop::UI
	 *  
	 * @return void
	 */
	public function load()
	{
		// Register back-end scripts
		add_action('admin_enqueue_scripts', array($this, 'registerScripts'));
	}

	/**
	 * Register MultiContentList scripts
	 */
	public function registerScripts()
	{
		$app_css_dependencies = array(
			'bebop-ui'
		);

		wp_register_style('bebop-ui--multilist', self::$__base_url .'/assets/css/bebop-ui--multilist.css', $app_css_dependencies);

		wp_register_script('bebop-ui--multilistView', self::$__base_url .'/assets/js/views/MultiList.js', array(), false, true);

		$app_dependencies = array(
			'jquery',
			'jquery-ui-tabs',
			'bebop-ui--multilistView'
		);		
		wp_register_script('bebop-ui--multilist', self::$__base_url .'/assets/js/bebop-ui--multilist.js', $app_dependencies, false, true);
	}

	/**
	 * Enqueues scripts that MultiContentList needs
	 * 
	 */
	private function __enqueueScripts()
	{
		wp_enqueue_style('bebop-ui--multilist');
		wp_enqueue_script('bebop-ui--multilist');
	}

	/**
	 * Creates single instance of the MultiContentList plugin
	 * 
	 * @param  string $title  Instance Title. Also used to create a slugified key
	 * @param  array  $config Configuration array
	 * @return object         Ponticlaro\Bebop\UI\Plugins\MultiContentList
	 */
	private function __createInstance($title, array $config = array())
	{	
		// Enqueue all scripts that the MultiContentList needs
		$this->__enqueueScripts();

		// Create slugified $key from $title
		$key = Bebop::util('slugify', $title);

		// Set default configuration values
		$this->__config->set(array(
			'key'   => $key,
			'title' => $title,
			'mode'  => 'default'
		));

		// Set configuration values from input
		$this->__config->set($config);

		return $this;
	}

	/**
	 * Adds a single list
	 * 
	 * @param \Ponticlaro\Bebop\UI\Plugins\ContentList $list  ContentList instance
	 */
	public function addList(\Ponticlaro\Bebop\UI\Plugins\ContentList $list, array $data = array())
	{
		// Override list data
		if ($data) $list->setData($data);

		// Store list
		$this->__lists->push($list);

		return $this;
	}

	/**
	 * Calls the internal renders function
	 * 
	 * @return object Ponticlaro\Bebop\UI\Plugins\MultiContentList
	 */
	public function render()
	{
		$this->__renderTemplate($this->__lists);

		return $this;
	}

	/**
	 * Renders template and lists
	 * 
	 * @param  \Ponticlaro\Bebop\Common\Collection $lists Lists collection
	 * @return void
	 */
	private function __renderTemplate(\Ponticlaro\Bebop\Common\Collection $lists)
	{
		include __DIR__ . '/templates/views/default/default.php';
	}
}