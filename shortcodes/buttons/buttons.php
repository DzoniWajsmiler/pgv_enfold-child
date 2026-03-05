<?php
/**
 * Button
 * 
 * Displays a colored button that links to any url of your choice
 */
if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly


if ( !class_exists( 'avia_sc_button' ) ) 
{
	class avia_sc_button extends aviaShortcodeTemplate
	{
			/**
			 * Create the config array for the shortcode button
			 */
			function shortcode_insert_button()
			{
				$this->config['name']		= __('Button', 'avia_framework' );
				$this->config['target']		= 'avia-target-insert';
				$this->config['shortcode'] 	= 'av_button';
				$this->config['preview'] 	= true;
			}

		
			/**
			 * Popup Elements
			 *
			 * If this function is defined in a child class the element automatically gets an edit button, that, when pressed
			 * opens a modal window that allows to edit the element properties
			 *
			 * @return void
			 */
			function popup_elements()
			{
				$this->elements = array(
					
					array(
							"type" 	=> "tab_container", 'nodescription' => true
						),
						

					
					array(	"name" 	=> __("Button Label", 'avia_framework' ),
							"desc" 	=> __("This is the text that appears on your button.", 'avia_framework' ),
				            "id" 	=> "label",
				            "type" 	=> "input",
				            "std" => __("Click me", 'avia_framework' )),
				    array(	
							"name" 	=> __("Button Link?", 'avia_framework' ),
							"desc" 	=> __("Where should your button link to?", 'avia_framework' ),
							"id" 	=> "link",
							"type" 	=> "linkpicker",
							"fetchTMPL"	=> true,
							"subtype" => array(	
												__('Set Manually', 'avia_framework' ) =>'manually',
												__('Single Entry', 'avia_framework' ) =>'single',
												__('Taxonomy Overview Page',  'avia_framework' )=>'taxonomy',
												),
							"std" 	=> ""),
							
					array(	
							"name" 	=> __("Open Link in new Window?", 'avia_framework' ),
							"desc" 	=> __("Select here if you want to open the linked page in a new window", 'avia_framework' ),
							"id" 	=> "link_target",
							"type" 	=> "select",
							"std" 	=> "",
							"subtype" => AviaHtmlHelper::linking_options()),
					
					array(	
							"name" 	=> __("Button Color", 'avia_framework' ),
							"desc" 	=> __("Choose a color for your button here", 'avia_framework' ),
							"id" 	=> "color",
							"type" 	=> "select",
							"std" 	=> "theme-color",
							"subtype" => array(
												__('Purple', 'avia_framework' )=>'purple',
												__('Pink', 'avia_framework' )=>'pink',
												
                                    ),
								),

					array(
							"type" 	=> "close_div",
							'nodescription' => true
						),
						
				);

			}
			

			/**
			 * Frontend Shortcode Handler
			 *
			 * @param array $atts array of attributes
			 * @param string $content text within enclosing form of shortcode element 
			 * @param string $shortcodename the shortcode found, when == callback name
			 * @return string $output returns the modified html string 
			 */
			function shortcode_handler($atts, $content = "", $shortcodename = "", $meta = "")
			{

			   $atts =  shortcode_atts(array('label' => 'Button',
                         'link' => '',
                         'link_target' => '',
                         'color' => 'purple',
                         ), $atts, $this->config['shortcode']);

                $color = 'purple';
                $target = "";

                if($atts['color'] === 'purple' || $atts['color'] === 'pink')
                    $color = $atts['color'];

                $link  = AviaHelper::get_url($atts['link']);
                $link  = ( ( $link == "http://" ) || ( $link == "manually" ) ) ? "" : $link;

                if($atts['link_target'])
                    $target = $atts['link_target'];

                $content .= '<div class="ow_button_container">';
                $content .= '<a class="ow-button custom_button button--1 color-'.$color.'" href="'.$link.'" target="'.$target.'">';
                $content .= $atts['label'];
                $content .= '<span class="button__container">';
                $content .= '<span class="circle top-left"></span>';
                $content .= '<span class="circle top-left"></span>';
                $content .= '<span class="circle top-left"></span>';
                $content .= '<span class="button__bg"></span>';
                $content .= '<span class="circle bottom-right"></span>';
                $content .= '<span class="circle bottom-right"></span>';
                $content .= '<span class="circle bottom-right"></span>';
                $content .= '</span>';
                $content .= '</a>';

                $content .= '</div>';

                return $content;
			}
			
			
			
	
	}
}
