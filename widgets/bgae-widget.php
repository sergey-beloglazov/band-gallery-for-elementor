<?php

use Elementor\Widget_Base;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Controls_Manager;
use Elementor\Plugin;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Band Gallery Widget.
 *
 * The Elementor single line (band) gallery widget addon will display images with different aspect ratios with the same height fulfilling the whole page or container width.
 *
 * @since 1.0.0
 */
class BGAE_Widget extends Widget_Base
{

	public function __construct($data = [], $args = null)
	{
		parent::__construct($data, $args);
		//Register CSS
		$this->bgae_register_styles();

		wp_register_script('bgae-lightbox.js', BGAE_URL  . 'assets/js/bgae-lightbox.js', ['elementor-frontend', 'jquery'], BGAE_VERSION, true);

		//Add actions and shortcodes
		$this->bgae_add_actions();

		//Add a widget icon to the Elementor panel
		$this->bgae_register_panel_styles();
		add_action('elementor/editor/after_enqueue_styles', [$this, 'bgae_enqueue_editor_panel_styles']);
	}

	//Add scripts to the Elementor panel
	public function bgae_enqueue_editor_panel_styles()
	{
		wp_enqueue_style('bgae-eicons-css');
	}


	/**
	 * Get widget name.
	 *
	 * Retrieve image gallery widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name()
	{
		return 'band-gallery-widget-addon';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve image gallery widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title()
	{
		return __('Band Image Gallery', 'bgae');
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve image gallery widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon()
	{
		return 'bgae-icon';
	}

	public function get_categories()
	{
		return ['bgae'];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords()
	{
		return ['image', 'photo', 'visual', 'gallery', 'portfolio'];
	}

	/**
	 * Register band image gallery widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 3.1.0
	 * @access protected
	 */
	protected function register_controls()
	{
		//* The content tab */
		//** The main section **/
		$this->start_controls_section(
			'bgae_layout_section',
			[
				'label' => esc_html__('Band Image Gallery', 'bgae'),
			]
		);

		$this->add_control(
			'bg_gallery',
			[
				'label' => esc_html__('Add Images', 'bgae'),
				'type' => Controls_Manager::GALLERY,
				'show_label' => false,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `thumbnail_size` and `thumbnail_custom_dimension`.
				'exclude' => ['custom'],
				'separator' => 'none',
			]
		);

		$this->add_control(
			'gallery_link',
			[
				'label' => esc_html__('Link', 'bgae'),
				'type' => Controls_Manager::SELECT,
				'default' => 'file',
				'options' => [
					'file' => esc_html__('Media File', 'bgae'),
					'attachment' => esc_html__('Attachment Page', 'bgae'),
					'none' => esc_html__('None', 'bgae'),
				],
			]
		);

		$this->add_control(
			'gallery_rand',
			[
				'label' => esc_html__('Order By', 'bgae'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => esc_html__('Default', 'bgae'),
					'rand' => esc_html__('Random', 'bgae'),
				],
				'default' => '',
			]
		);

		$this->add_control(
			'view',
			[
				'label' => esc_html__('View', 'bgae'),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'traditional',
			]
		);

		$this->end_controls_section();
		//** /The main section **/

		//** The lightbox section **/
		$this->start_controls_section(
			'section_lightbox',
			[
				'label' => esc_html__('Lightbox', 'bgae'),
				'condition' => [
					'gallery_link' => 'file',
				],
			]
		);
		$this->add_control(
			'open_lightbox',
			[
				'label' => esc_html__('Lightbox', 'bgae'),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => esc_html__('Default', 'bgae'),
					'yes' => esc_html__('Yes', 'bgae'),
					'no' => esc_html__('No', 'bgae'),
				],
			]
		);

		//Lightbox captions parameters
		$this->add_control(
			'lightbox_title_is_link',
			[
				'label' => esc_html__('Title is a link', 'bgae'),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'return_value' => 'cursor:  pointer;',
				'description' => esc_html__('The Lightbox image title is a hyperlink to the media page', 'bgae'),
				'selectors' => [
					'#elementor-lightbox-slideshow-{{ID}} .elementor-slideshow__title' => '{{VALUE}}',
				],
				'render_type' => 'template',
				'frontend_available' => true,
			]
		);
		$this->add_control(
			'lightbox_title_color',
			[
				'label' => esc_html__('Title color', 'bgae'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'#elementor-lightbox-slideshow-{{ID}} .elementor-slideshow__title' => 'color: {{VALUE}}',
				],
				'render_type' => 'template',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'lightbox_description_is_link',
			[
				'label' => esc_html__('Description is a link', 'bgae'),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'return_value' => 'cursor:  pointer;',
				'description' => esc_html__('The Lightbox image description is a hyperlink to the media page', 'bgae'),
				'selectors' => [
					'#elementor-lightbox-slideshow-{{ID}} .elementor-slideshow__description' => '{{VALUE}}',
				],
				'render_type' => 'template',
				'frontend_available' => true,
			]
		);
		$this->add_control(
			'lightbox_description_color',
			[
				'label' => esc_html__('Description color', 'bgae'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'#elementor-lightbox-slideshow-{{ID}} .elementor-slideshow__description' => 'color: {{VALUE}}',
				],
				'render_type' => 'template',
				'frontend_available' => true,
			]
		);


		$this->end_controls_section();
		//** /The lightbox section **/

		//* The style tab */
		//**  The images section **/
		$this->start_controls_section(
			'section_gallery_images',
			[
				'label' => esc_html__('Images', 'bgae'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'image_spacing',
			[
				'label' => esc_html__('Spacing', 'bgae'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => esc_html__('Default', 'bgae'),
					'custom' => esc_html__('Custom', 'bgae'),
				],
				'prefix_class' => 'gallery-spacing-',
				'default' => '',
			]
		);
		//A horizontal margin doesn't work for gallery in Airi theme
		$columns_margin = is_rtl() ? '0 0 -{{SIZE}}{{UNIT}} 0;' : '0 0 -{{SIZE}}{{UNIT}} 0;';
		$columns_padding = is_rtl() ? '0 0 {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}};' : '0 {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0;';

		$this->add_control(
			'image_spacing_custom',
			[
				'label' => esc_html__('Image Spacing', 'bgae'),
				'type' => Controls_Manager::SLIDER,
				'show_label' => false,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'default' => [
					'size' => 15,
				],
				'selectors' => [
					'{{WRAPPER}} .gallery-item' => 'padding:' . $columns_padding,
					'{{WRAPPER}} .gallery' => 'margin: ' . $columns_margin,
				],
				'condition' => [
					'image_spacing' => 'custom',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'image_border',
				'selector' => '{{WRAPPER}} .gallery-item img',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'image_border_radius',
			[
				'label' => esc_html__('Border Radius', 'bgae'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .gallery-item img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
		//**  /The images section **/

		//**  The caption section **/
		$this->start_controls_section(
			'section_caption',
			[
				'label' => esc_html__('Caption', 'bgae'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'gallery_display_caption',
			[
				'label' => esc_html__('Display', 'bgae'),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => esc_html__('Show', 'bgae'),
					'none' => esc_html__('Hide', 'bgae'),
				],
				'selectors' => [
					'{{WRAPPER}} .gallery-item .gallery-caption' => 'display: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'align',
			[
				'label' => esc_html__('Alignment', 'bgae'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__('Left', 'bgae'),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'bgae'),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', 'bgae'),
						'icon' => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => esc_html__('Justified', 'bgae'),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .gallery-item .gallery-caption' => 'text-align: {{VALUE}};',
				],
				'condition' => [
					'gallery_display_caption' => '',
				],
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => esc_html__('Text Color', 'bgae'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .gallery-item .gallery-caption' => 'color: {{VALUE}};',
				],
				'condition' => [
					'gallery_display_caption' => '',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				],
				'selector' => '{{WRAPPER}} .gallery-item .gallery-caption',
				'condition' => [
					'gallery_display_caption' => '',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'caption_shadow',
				'selector' => '{{WRAPPER}} .gallery-item .gallery-caption',
			]
		);

		$this->end_controls_section();
		//**  /The caption section **/
	}

	/**
	 * Registers front end styles
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function bgae_register_styles()
	{
		//Register 
		$this->bgae_register_file('bgae-image-gallery-css', 'bgae-image-gallery.css');
	}

	/**
	 * Registers front end styles for the Elementor panel
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function bgae_register_panel_styles()
	{
		//Register 
		$this->bgae_register_file('bgae-eicons-css', 'bgae-eicons.css');
	}

	/**
	 * Registers a style or a script file, using a file timestamp for clearing an old version from browser cache
	 *
	 * @param string $handle Name of the stylesheet. Should be unique.
	 * @param string $file Name of the file without a path
	 * @param string $file_type Should be 'css' or 'js' for corresponding type of the file 
	 * 
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function bgae_register_file($handle, $file_name, $file_type = "css")
	{
		//Check allowed file types
		if (!in_array($file_type, ['js', 'css'])) {
			return;
		}
		//Get a file's path
		$file_path = dirname(__DIR__) . '/assets/' . $file_type . '/' . $file_name;
		//File's path
		$fileUrl = BGAE_URL  . 'assets/' . $file_type . '/' . $file_name;
		//Make a timestamp from file last modification time
		$timestamp = filemtime($file_path);

		//Check file type
		if ($file_type === 'js') {
			wp_register_script($handle, $fileUrl, [], $timestamp, true);
		} elseif ($file_type === 'css') {
			wp_register_style($handle, $fileUrl,  [], $timestamp, 'all');
		}
	}

	public function get_script_depends()
	{
		return ['bgae-lightbox.js'];
	}

	public function get_style_depends()
	{

		return ['bgae-image-gallery-css'];
	}

	/**
	 * Add actions and shortcodes
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function bgae_add_actions()
	{
		//Add the Band Gallery shortcode
		add_shortcode('band_gallery', [$this, 'band_gallery_shortcode_render']);

		//Add scripts to the Elementor panel
		add_action('elementor/editor/before_enqueue_scripts', [$this, 'bgae_register_panel_styles']);
	}



	/**
	 * Builds the Band Gallery shortcode output.
	 *
	 * This implements the functionality of the Band Gallery Shortcode 'band_gallery' for displaying images.
	 *
	 * @since 1.0.0
	 *
	 * @param array $attr {
	 *     Attributes of the gallery shortcode.
	 *
	 *     @type string       $order      Order of the images in the gallery. Default 'ASC'. Accepts 'ASC', 'DESC'.
	 *     @type string       $orderby    The field to use when ordering the images. Default 'menu_order ID'.
	 *                                    Accepts any valid SQL ORDERBY statement.
	 *     @type int          $id         Post ID.
	 *     @type string       $itemtag    HTML tag to use for each image in the gallery.
	 *                                    Default 'dl', or 'figure' when the theme registers HTML5 gallery support.
	 *     @type string       $icontag    HTML tag to use for each image's icon.
	 *                                    Default 'dt', or 'div' when the theme registers HTML5 gallery support.
	 *     @type string       $captiontag HTML tag to use for each image's caption.
	 *                                    Default 'dd', or 'figcaption' when the theme registers HTML5 gallery support.
	 *     @type string|int[] $size       Size of the images to display. Accepts any registered image size name, or an array
	 *                                    of width and height values in pixels (in that order). Default 'thumbnail'.
	 *     @type string       $ids        A comma-separated list of IDs of attachments to display. Default empty.
	 *     @type string       $include    A comma-separated list of IDs of attachments to include. Default empty.
	 *     @type string       $exclude    A comma-separated list of IDs of attachments to exclude. Default empty.
	 *     @type string       $link       What to link each image to. Default empty (links to the attachment page).
	 *                                    Accepts 'file', 'none'.
	 * }
	 * @return string HTML content to display gallery.
	 * @access private
	 * 
	 */
	function band_gallery_shortcode_render($attr)
	{
		$post = get_post();

		static $instance = 0;
		$instance++;

		if (!empty($attr['ids'])) {
			// 'ids' is explicitly ordered, unless you specify otherwise.
			if (empty($attr['orderby'])) {
				$attr['orderby'] = 'post__in';
			}
			$attr['include'] = $attr['ids'];
		}

		/**
		 * Filters the default gallery shortcode output.
		 *
		 * If the filtered output isn't empty, it will be used instead of generating
		 * the default gallery template.
		 *
		 * @since 2.5.0
		 * @since 4.2.0 The `$instance` parameter was added.
		 *
		 * @see gallery_shortcode()
		 *
		 * @param string $output   The gallery output. Default empty.
		 * @param array  $attr     Attributes of the gallery shortcode.
		 * @param int    $instance Unique numeric ID of this gallery shortcode instance.
		 */
		$output = apply_filters('post_gallery', '', $attr, $instance);

		if (!empty($output)) {
			return $output;
		}

		$html5 = current_theme_supports('html5', 'gallery');
		$atts  = shortcode_atts(
			array(
				'order'      => 'ASC',
				'orderby'    => 'menu_order ID',
				'id'         => $post ? $post->ID : 0,
				'itemtag'    => $html5 ? 'figure' : 'dl',
				'icontag'    => $html5 ? 'div' : 'dt',
				'captiontag' => $html5 ? 'figcaption' : 'dd',
				'columns'    => 3,
				'size'       => 'thumbnail',
				'include'    => '',
				'exclude'    => '',
				'link'       => '',
			),
			$attr,
			'gallery'
		);

		$id = (int) $atts['id'];

		if (!empty($atts['include'])) {
			$_attachments = get_posts(
				array(
					'include'        => $atts['include'],
					'post_status'    => 'inherit',
					'post_type'      => 'attachment',
					'post_mime_type' => 'image',
					'order'          => $atts['order'],
					'orderby'        => $atts['orderby'],
				)
			);

			$attachments = array();
			foreach ($_attachments as $key => $val) {
				$attachments[$val->ID] = $_attachments[$key];
			}
		} elseif (!empty($atts['exclude'])) {
			$attachments = get_children(
				array(
					'post_parent'    => $id,
					'exclude'        => $atts['exclude'],
					'post_status'    => 'inherit',
					'post_type'      => 'attachment',
					'post_mime_type' => 'image',
					'order'          => $atts['order'],
					'orderby'        => $atts['orderby'],
				)
			);
		} else {
			$attachments = get_children(
				array(
					'post_parent'    => $id,
					'post_status'    => 'inherit',
					'post_type'      => 'attachment',
					'post_mime_type' => 'image',
					'order'          => $atts['order'],
					'orderby'        => $atts['orderby'],
				)
			);
		}

		if (empty($attachments)) {
			return '';
		}

		if (is_feed()) {
			$output = "\n";
			foreach ($attachments as $att_id => $attachment) {
				if (!empty($atts['link'])) {
					if ('none' === $atts['link']) {
						$output .= wp_get_attachment_image($att_id, $atts['size'], false, $attr);
					} else {
						$output .= wp_get_attachment_link($att_id, $atts['size'], false);
					}
				} else {
					$output .= wp_get_attachment_link($att_id, $atts['size'], true);
				}
				$output .= "\n";
			}
			return $output;
		}

		$itemtag    = tag_escape($atts['itemtag']);
		$captiontag = tag_escape($atts['captiontag']);
		$icontag    = tag_escape($atts['icontag']);
		$valid_tags = wp_kses_allowed_html('post');
		if (!isset($valid_tags[$itemtag])) {
			$itemtag = 'dl';
		}
		if (!isset($valid_tags[$captiontag])) {
			$captiontag = 'dd';
		}
		if (!isset($valid_tags[$icontag])) {
			$icontag = 'dt';
		}


		$selector = "band-gallery-{$instance}";

		$band_gallery_style = '';

		/**
		 * Filters whether to print default gallery styles.
		 *
		 * @since 3.1.0
		 *
		 * @param bool $print Whether to print default gallery styles.
		 *                    Defaults to false if the theme supports HTML5 galleries.
		 *                    Otherwise, defaults to true.
		 */
		if (apply_filters('use_default_gallery_style', !$html5)) {
			$type_attr = current_theme_supports('html5', 'style') ? '' : ' type="text/css"';

			$band_gallery_style = "
		<style{$type_attr}>
			#{$selector} {
				margin: auto;
				display: flex;
			}
			#{$selector} .gallery-item {
				margin-top: 10px;
				text-align: center;
			}
			#{$selector} img {
				border: 1px solid #cfcfcf;
			}
			#{$selector} .gallery-caption {
				margin-left: 0;
			}
			#{$selector} .gallery-item:last-of-type {
				padding-right: 0;
			  }			
			/* see gallery_shortcode() in wp-includes/media.php */
		</style>\n\t\t";
		}

		$size_class  = sanitize_html_class(is_array($atts['size']) ? implode('x', $atts['size']) : $atts['size']);
		$gallery_div = "<div id='$selector' class='band-gallery gallery galleryid-{$id} gallery-size-{$size_class}'>";

		/**
		 * Filters the default gallery shortcode CSS styles.
		 *
		 * @since 2.5.0
		 *
		 * @param string $gallery_style Default CSS styles and opening HTML div container
		 *                              for the gallery shortcode output.
		 */
		$output = apply_filters('band-gallery_style', $band_gallery_style . $gallery_div);

		// /The band gallery

		$i = 0;

		//The total number of images
		$total_images = count($attachments);
		foreach ($attachments as $id => $attachment) {

			$attr = (trim($attachment->post_excerpt)) ? array('aria-describedby' => "$selector-$id") : '';

			if (!empty($atts['link']) && 'file' === $atts['link']) {
				$image_output = wp_get_attachment_link($id, $atts['size'], false, false, false, $attr);
			} elseif (!empty($atts['link']) && 'none' === $atts['link']) {
				$image_output = wp_get_attachment_image($id, $atts['size'], false, $attr);
			} else {
				$image_output = wp_get_attachment_link($id, $atts['size'], true, false, false, $attr);
			}


			$image_meta = wp_get_attachment_metadata($id);


			$orientation = '';

			if (isset($image_meta['height'], $image_meta['width'])) {
				$orientation = ($image_meta['height'] > $image_meta['width']) ? 'portrait' : 'landscape';
			}
			//Use an image ratio as a flex grow factor
			$ratio = $image_meta['width'] / $image_meta['height'];
			//Use flex-grow=1 if there is only one image
			if ($total_images == 1) {
				$ratio = 1;
			}

			$output .= "<{$itemtag} class='gallery-item'  style='flex: " . ($ratio) . "'>";
			$output .= "
			<{$icontag} class='gallery-icon {$orientation}'>
				$image_output
			</{$icontag}>";

			if ($captiontag && trim($attachment->post_excerpt)) {
				$output .= "
				<{$captiontag} class='wp-caption-text gallery-caption' id='$selector-$id'>
				" . wptexturize($attachment->post_excerpt) . "
				</{$captiontag}>";
			}

			$output .= "</{$itemtag}>";
		}

		$output .= "
		</div>\n";
		// /The band gallery

		return $output;
	}



	/**
	 * Render image gallery widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render()
	{
		$settings = $this->get_settings_for_display();

		if (!$settings['bg_gallery']) {
			return;
		}

		$ids = wp_list_pluck($settings['bg_gallery'], 'id');

		$this->add_render_attribute('shortcode', 'ids', implode(',', $ids));
		$this->add_render_attribute('shortcode', 'size', $settings['thumbnail_size']);

		//TODO: move to the whole gallery data
		//Add a link flag for a lightbox image title
		if (!empty($settings['lightbox_title_is_link'])) {
			$this->add_render_attribute('link', 'data-lightbox-title-is-link', 'yes');
		}
		//Add a link flag for a lightbox image description
		if (!empty($settings['lightbox_description_is_link'])) {
			$this->add_render_attribute('link', 'data-lightbox-description-is-link', 'yes');
		}

		if ($settings['gallery_link']) {
			$this->add_render_attribute('shortcode', 'link', $settings['gallery_link']);
		}

		if (!empty($settings['gallery_rand'])) {
			$this->add_render_attribute('shortcode', 'orderby', $settings['gallery_rand']);
		}
?>
		<div class="elementor-image-gallery">
			<?php
			add_filter('wp_get_attachment_link', [$this, 'add_lightbox_data_to_image_link'], 10, 2);

			echo do_shortcode('[band_gallery ' . $this->get_render_attribute_string('shortcode') . ']');

			remove_filter('wp_get_attachment_link', [$this, 'add_lightbox_data_to_image_link']);
			?>
		</div>
<?php
	}

	/**
	 * Add Light-Box attributes.
	 *
	 * Used to add Light-Box-related data attributes to links that open media files.
	 *
	 * @param array|string $element         The link HTML element.
	 * @param int $id                       The ID of the image
	 * @param string $lightbox_setting_key  The setting key that dictates weather to open the image in a lightbox
	 * @param string $group_id              Unique ID for a group of lightbox images
	 * @param bool $overwrite               Optional. Whether to overwrite existing
	 *                                      attribute. Default is false, not to overwrite.
	 *
	 * @return Widget_Base Current instance of the widget.
	 * @since 2.9.0
	 * @access public
	 *
	 */
	public function add_lightbox_data_attributes($element, $id = null, $lightbox_setting_key = null, $group_id = null, $overwrite = false)
	{

		$kit = Plugin::$instance->kits_manager->get_active_kit();

		$is_global_image_lightbox_enabled = 'yes' === $kit->get_settings('global_image_lightbox');

		if ('no' === $lightbox_setting_key) {
			if ($is_global_image_lightbox_enabled) {
				$this->add_render_attribute($element, 'data-elementor-open-lightbox', 'no', $overwrite);
			}

			return $this;
		}

		if ('yes' !== $lightbox_setting_key && !$is_global_image_lightbox_enabled) {
			return $this;
		}

		$attributes['data-elementor-open-lightbox'] = 'yes';

		if ($group_id) {
			$attributes['data-elementor-lightbox-slideshow'] = $group_id;
		}

		if ($id) {
			$lightbox_image_attributes = Plugin::$instance->images_manager->get_lightbox_image_attributes($id);

			//TODO: check if the link to be used in texts
			//Get attachment page
			$attachment_page_link = get_attachment_link($id);
			$attributes['data-bgae-lightbox-page-url'] = $attachment_page_link;

			if (isset($lightbox_image_attributes['title'])) {
				$attributes['data-elementor-lightbox-title'] = $lightbox_image_attributes['title'];
			}


			if (isset($lightbox_image_attributes['description'])) {
				$attributes['data-elementor-lightbox-description'] = $lightbox_image_attributes['description'];
			}
		}

		$this->add_render_attribute($element, $attributes, null, $overwrite);

		return $this;
	}
}
