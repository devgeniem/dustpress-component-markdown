<?php
/**
 * Plugin Name: DustPress Component: Markdown
 * Plugin URI: https://github.com/devgeniem/dustpress-components-markdown
 * Description: Markdown component for DustPress Components
 * Version: 0.0.3
 * Author: Geniem Oy / Miika Arponen
 * Author URI: http://www.geniem.com
 */

namespace DustPress\Components;

require_once('dpc-linked-md.php');

\add_action( 'plugins_loaded', function() {


	class Markdown extends Component {

		static $parsedown;

		/**
		 * Variables
		 * label = Name of the component shows in admin side
		 * name  = ACF field slug
		 * key   = add component name
		 */
		var $label 	= 'Markdown';
		var $name 	= 'markdown';
		var $key 	= 'dpc_markdown';

		public function init() {

			require_once('parsedown.php');

			if ( ! isset( self::$parsedown ) ) {
				self::$parsedown = new \Parsedown();
			}			

			wp_enqueue_script( 'highlight.js', $this->url . 'dist/highlight.pack.js', null, false, false );
			wp_enqueue_style( 'highlight.js', $this->url . 'dist/github.css' );

			// Fixes html entity with ACF fields type of 'niche_markdown'.
			\add_filter('acf/format_value/type=niche_markdown', function( $value, $post_id, $field ) {

				$value = html_entity_decode( $value );

				return $value;
			}, 999, 3);
		}

		/**
		 * acf field component data
		 * @param  [type] $data
		 * @return [type] [description]
		 * 
		 * !remember to describe acf field names!
		 * $data['field_slug'] = description
		 */
		public function data( $data ) {
			$data['m'] = self::$parsedown->text( $data['m'] );

			return $data;
		}

		/**
		 * 
		 * @return [type] [description]
		 */
		public function before() {
		}

		/**
		 * [after description]
		 * @return [type] [description]
		 */
		public function after() {
			wp_enqueue_script( 'dustpress-components-component-name' );
		}

		/**
		 * ACF fields
		 * @return [type] [description]
		 */
		public function fields() {
			return array (
				'key' => 'dpc_markdown',
				'name' => $this->name,
				'label' => $this->label,
				'display' => 'block',
				'sub_fields' => array (
					array (
						'key' => 'dpc_markdown',
						'label' => 'Markdown',
						'name' => 'm',
						'type' => 'niche_markdown',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array (
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'autogrow' => 1,
						'editor-theme' => 'light',
						'preview-theme' => 'github',
						'syntax-highlight' => 1,
						'syntax-theme' => 'github',
						'media-upload' => 0,
						'tab-function' => 0,
					),
				),
				'min' => '',
				'max' => '',
			);
		}
	}
	
	Components::add( new Markdown() );
}, 2, 1 );