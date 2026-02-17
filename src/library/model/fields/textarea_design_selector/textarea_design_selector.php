<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.
/**
 *
 * Field: textarea_design_selector
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'AGWISHGLUT_textarea_design_selector' ) ) {
	class AGWISHGLUT_textarea_design_selector extends AGWISHGLUTP {

		public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
			parent::__construct( $field, $value, $unique, $where, $parent );
		}

		public function render() {

			$args = wp_parse_args( $this->field, array(
				'id' => false,
				'designs' => array(),
			) );

			echo wp_kses_post( $this->field_before() );

			$default_designs = array(
				'simple_list' => array(
					'title' => __('Simple List', 'wishglut'),
					'description' => __('Each line displayed as separate item', 'wishglut'),
					'demo' => '<div class="simple-list-demo">
						<div>• First feature point</div>
						<div>• Second feature point</div>
						<div>• Third feature point</div>
					</div>'
				),
				'bullet_points' => array(
					'title' => __('Bullet Points', 'wishglut'),
					'description' => __('Styled bullet points with icons', 'wishglut'),
					'demo' => '<div class="bullet-points-demo">
						<div class="bullet-item">✓ Premium quality materials</div>
						<div class="bullet-item">✓ Fast worldwide shipping</div>
						<div class="bullet-item">✓ 30-day money back guarantee</div>
					</div>'
				),
				'numbered_list' => array(
					'title' => __('Numbered List', 'wishglut'),
					'description' => __('Sequential numbered items', 'wishglut'),
					'demo' => '<div class="numbered-list-demo">
						<div class="numbered-item"><span class="number">1</span> Step one process</div>
						<div class="numbered-item"><span class="number">2</span> Step two process</div>
						<div class="numbered-item"><span class="number">3</span> Step three process</div>
					</div>'
				),
				'paragraphs' => array(
					'title' => __('Paragraphs', 'wishglut'),
					'description' => __('Formatted paragraph text', 'wishglut'),
					'demo' => '<div class="paragraphs-demo">
						<div style="font-size: 13px; line-height: 1.4;">This is a well-formatted paragraph with proper spacing and text alignment. Perfect for detailed descriptions.</div>
					</div>'
				),
				'cards' => array(
					'title' => __('Cards Grid', 'wishglut'),
					'description' => __('Responsive card layout', 'wishglut'),
					'demo' => '<div class="cards-demo" style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
						<div style="background: #f8f9fa; border: 1px solid #e9ecef; padding: 6px; border-radius: 4px; font-size: 11px;">Feature 1</div>
						<div style="background: #f8f9fa; border: 1px solid #e9ecef; padding: 6px; border-radius: 4px; font-size: 11px;">Feature 2</div>
					</div>'
				),
				'features_grid' => array(
					'title' => __('Features Grid', 'wishglut'),
					'description' => __('Checkmark features in grid', 'wishglut'),
					'demo' => '<div class="features-demo" style="display: grid; grid-template-columns: 1fr 1fr; gap: 6px;">
						<div style="background: #e8f5e8; padding: 4px; border-radius: 3px; font-size: 11px; display: flex; align-items: center;">✓ Feature A</div>
						<div style="background: #e8f5e8; padding: 4px; border-radius: 3px; font-size: 11px; display: flex; align-items: center;">✓ Feature B</div>
					</div>'
				),
				'info_boxes' => array(
					'title' => __('Info Boxes', 'wishglut'),
					'description' => __('Gradient info boxes with icons', 'wishglut'),
					'demo' => '<div class="info-boxes-demo" style="display: flex; flex-direction: column; gap: 4px;">
						<div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 6px; border-radius: 4px; font-size: 11px;">ℹ Important information</div>
						<div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 6px; border-radius: 4px; font-size: 11px;">ℹ Additional details</div>
					</div>'
				),
				'tags' => array(
					'title' => __('Tags', 'wishglut'),
					'description' => __('Pill-shaped tag elements', 'wishglut'),
					'demo' => '<div class="tags-demo" style="display: flex; gap: 4px; flex-wrap: wrap;">
						<span style="background: #e3f2fd; color: #1976d2; padding: 2px 6px; border-radius: 10px; font-size: 10px; border: 1px solid #bbdefb;">Tag 1</span>
						<span style="background: #e3f2fd; color: #1976d2; padding: 2px 6px; border-radius: 10px; font-size: 10px; border: 1px solid #bbdefb;">Tag 2</span>
						<span style="background: #e3f2fd; color: #1976d2; padding: 2px 6px; border-radius: 10px; font-size: 10px; border: 1px solid #bbdefb;">Tag 3</span>
					</div>'
				),
				'timeline' => array(
					'title' => __('Timeline', 'wishglut'),
					'description' => __('Vertical timeline layout', 'wishglut'),
					'demo' => '<div class="timeline-demo" style="position: relative; padding-left: 20px;">
						<div style="position: absolute; left: 6px; top: 0; bottom: 0; width: 1px; background: #e0e0e0;"></div>
						<div style="position: relative; margin-bottom: 8px;">
							<div style="position: absolute; left: -14px; top: 2px; width: 8px; height: 8px; border-radius: 50%; background: #4CAF50; border: 2px solid white; box-shadow: 0 0 0 1px #e0e0e0;"></div>
							<div style="background: #f5f5f5; padding: 4px 6px; border-radius: 3px; font-size: 11px; border-left: 2px solid #4CAF50;">Timeline item 1</div>
						</div>
						<div style="position: relative;">
							<div style="position: absolute; left: -14px; top: 2px; width: 8px; height: 8px; border-radius: 50%; background: #4CAF50; border: 2px solid white; box-shadow: 0 0 0 1px #e0e0e0;"></div>
							<div style="background: #f5f5f5; padding: 4px 6px; border-radius: 3px; font-size: 11px; border-left: 2px solid #4CAF50;">Timeline item 2</div>
						</div>
					</div>'
				),
			);

			$designs = wp_parse_args( $args['designs'], $default_designs );
			$current_value = $this->value ?? 'simple_list';

			?>
			<div class="wishglut-textarea-design-selector">
				<style>
				.wishglut-textarea-design-selector {
					width: 100%;
				}
				.wishglut-design-options {
					display: grid;
					grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
					gap: 15px;
					margin-top: 10px;
				}
				.wishglut-design-option {
					border: 2px solid #e0e0e0;
					border-radius: 8px;
					padding: 15px;
					cursor: pointer;
					transition: all 0.3s ease;
					background: white;
					position: relative;
				}
				.wishglut-design-option:hover {
					border-color: #0073aa;
					box-shadow: 0 4px 12px rgba(0,115,170,0.15);
					transform: translateY(-2px);
				}
				.wishglut-design-option.selected {
					border-color: #0073aa;
					background-color: #f7fcff;
				}
				.wishglut-design-option.selected::after {
					content: '✓';
					position: absolute;
					top: 8px;
					right: 8px;
					width: 24px;
					height: 24px;
					background: #0073aa;
					color: white;
					border-radius: 50%;
					display: flex;
					align-items: center;
					justify-content: center;
					font-size: 14px;
					font-weight: bold;
					box-shadow: 0 2px 4px rgba(0,115,170,0.3);
					z-index: 1;
				}
				.design-title {
					font-weight: 600;
					margin-bottom: 10px;
					color: #333;
					font-size: 14px;
				}
				.design-demo {
					background: #f9f9f9;
					padding: 12px;
					border-radius: 4px;
					margin-bottom: 10px;
					border: 1px solid #e0e0e0;
					min-height: 80px;
				}
				.design-description {
					font-size: 12px;
					color: #666;
					line-height: 1.4;
				}

				/* Demo Styles */
				.simple-list-demo div {
					padding: 2px 0;
					font-size: 13px;
				}
				.bullet-points-demo .bullet-item {
					padding: 3px 0;
					font-size: 13px;
					color: #2e7d32;
				}
				.feature-cards-demo {
					display: flex;
					flex-direction: column;
					gap: 6px;
				}
				.feature-card {
					background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
					color: white;
					padding: 4px 8px;
					border-radius: 4px;
					font-size: 11px;
					text-align: center;
				}
				.numbered-list-demo .numbered-item {
					display: flex;
					align-items: center;
					padding: 3px 0;
					font-size: 13px;
				}
				.number {
					background: #0073aa;
					color: white;
					width: 16px;
					height: 16px;
					border-radius: 50%;
					display: flex;
					align-items: center;
					justify-content: center;
					font-size: 10px;
					margin-right: 8px;
					flex-shrink: 0;
				}
				</style>

				<div class="wishglut-design-options">
					<?php foreach ( $designs as $design_key => $design ): ?>
						<div class="wishglut-design-option <?php echo ($current_value === $design_key) ? 'selected' : ''; ?>"
							 data-design="<?php echo esc_attr( $design_key ); ?>">
							<div class="design-title"><?php echo esc_html( $design['title'] ); ?></div>
							<div class="design-demo">
								<?php echo wp_kses_post( $design['demo'] ); ?>
							</div>
							<div class="design-description"><?php echo esc_html( $design['description'] ); ?></div>
						</div>
					<?php endforeach; ?>
				</div>

				<input type="hidden"
					   name="<?php echo esc_attr( $this->field_name() ); ?>"
					   value="<?php echo esc_attr( $current_value ); ?>"
					   <?php echo wp_kses_post( $this->field_attributes() ); ?>>

				<script>
				// Use jQuery for better compatibility with WordPress admin
				jQuery(document).ready(function($) {
					// Handle click on design options
					$('.wishglut-textarea-design-selector .wishglut-design-option').on('click', function() {
						var $this = $(this);
						var design = $this.data('design');
						var $container = $this.closest('.wishglut-textarea-design-selector');

						// Remove selected class from all options in this container
						$container.find('.wishglut-design-option').removeClass('selected');

						// Add selected class to clicked option
						$this.addClass('selected');

						// Update hidden input value
						var $input = $container.find('input[type="hidden"]');
						if ($input.length) {
							$input.val(design);
						}
					});
				});
				</script>
			</div>

			<?php

			echo wp_kses_post( $this->field_after() );

		}

	}
}