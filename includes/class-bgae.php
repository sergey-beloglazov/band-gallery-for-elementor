<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

// Add a custom category for panel widgets
add_action('elementor/init', function () {
	\Elementor\Plugin::$instance->elements_manager->add_category(
		'bgae',				 // the name of the category
		[
			'title' => esc_html__('Internet-mir', 'bgae'),
			'icon' => 'fa fa-header', //default icon
		],
		1 // position
	);
});

/**
 * Main Plugin Class
 *
 * Register new elementor widget.
 *
 * @since 1.0.0
 */
class BGAE_WidgetClass
{

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function __construct()
	{
		$this->bgae_add_actions();
	}

	/**
	 * Add Actions
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function bgae_add_actions()
	{
		//Register the widget
		add_action('elementor/widgets/register', array($this, 'bgae_on_register_widget'));
	}


	/**
	 * Register the widget on widgets register
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * 
	 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
	 */
	public function bgae_on_register_widget($widgets_manager)
	{
		$this->bgae_widget_includes();
		$bgaeWidget = new \BGAE_Widget();
		$widgets_manager->register($bgaeWidget);
	}

	/**
	 * On Widgets Registered
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function bgae_on_widgets_registered()
	{
		$this->bgae_widget_includes();
	}

	/**
	 * Includes
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function bgae_widget_includes()
	{
		require_once BGAE_PATH . 'widgets/bgae-widget.php';
	}
}

new BGAE_WidgetClass();
