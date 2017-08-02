<?php
namespace DustPress\Components;

\add_action( 'plugins_loaded', function() {


	class LinkedMarkdown extends Component {

		static $parsedown;

		static $search_cache;

		/**
		 * Variables
		 * label = Name of the component shows in admin side
		 * name  = ACF field slug
		 * key   = add component name
		 */
		var $label 	= 'Linked Markdown';
		var $name 	= 'linkedmarkdown';
		var $key 	= 'dpc_linkedmarkdown';

		public function init() {
			require_once('parsedown.php');

			if ( ! isset( self::$parsedown ) ) {
				self::$parsedown = new \Parsedown();
			}

			wp_enqueue_script( 'highlight.js', $this->url . 'dist/highlight.pack.js', null, false, false );
			wp_enqueue_style( 'highlight.js', $this->url . 'dist/github.css' );
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
            try {
                $markdown = file_get_contents( $data['url'] );

                \set_transient( 'dpc/linkedmarkdown/' . $data['url'], $markdown, MONTH_IN_SECONDS );

                $data['m'] = self::$parsedown->text( $markdown );
            }
            catch( Exception $e ) {
                if ( $markdown = get_transient( 'dpc/linkedmarkdown/' . $data['url'] ) ) {
                    $data['m'] = self::$parsedown->text( $markdown );
                }
                else {
                    $data['m'] = '<p>File couldn\'t be fetched.</p>';
                }
            }

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
			\update_post_meta( get_the_ID(), '_search_cache', self::$search_cache );
		}

		/**
		 * ACF fields
		 * @return [type] [description]
		 */
		public function fields() {
			return array (
				'key' => 'dpc_linkedmarkdown',
				'name' => $this->name,
				'label' => $this->label,
				'display' => 'block',
				'sub_fields' => array (
					array (
						'key' => 'dpc_url',
						'label' => 'URL',
						'name' => 'url',
						'type' => 'url',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array (
							'width' => '',
							'class' => '',
							'id' => '',
						),
					),
				),
				'min' => '',
				'max' => '',
			);
		}
	}
	
	Components::add( new LinkedMarkdown() );
}, 2, 1 );