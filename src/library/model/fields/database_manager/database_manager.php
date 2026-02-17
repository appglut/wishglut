<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.
/**
 *
 * Field: database_manager
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'AGWISHGLUT_database_manager' ) ) {
	class AGWISHGLUT_database_manager extends AGWISHGLUTP {

		public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
			parent::__construct( $field, $value, $unique, $where, $parent );
		}

		public function render() {
			global $wpdb;

			// Get all table definitions from DataManage class
			$all_tables = \Shopglut\DataManage::get_all_table_names();

			// Check which tables exist
			$tables_status = array();
			foreach ( $all_tables as $key => $table_info ) {
				$table_name = $table_info['table'];
				$exists = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) ) === $table_name;

				// Get row count if table exists
				$row_count = 0;
				if ( $exists ) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Getting row count
					$row_count = $wpdb->get_var( "SELECT COUNT(*) FROM `{$table_name}`" );
				}

				$tables_status[ $key ] = array(
					'info' => $table_info,
					'exists' => $exists,
					'row_count' => $row_count
				);
			}

			echo wp_kses_post( $this->field_before() );
			?>
			<div class="shopglut-table-manager" style="margin-top: 10px;">
				<!-- Table Selection -->
				<div class="shopglut-table-list" style="background: #f9f9f9; border: 1px solid #ddd; border-radius: 4px; padding: 15px; margin-bottom: 15px;">
					<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
						<strong><?php esc_html_e( 'Select tables to delete:', 'shopglut' ); ?></strong>
						<label style="cursor: pointer;">
							<input type="checkbox" id="shopglut-select-all-tables" style="margin-right: 5px;">
							<?php esc_html_e( 'Select All', 'shopglut' ); ?>
						</label>
					</div>

					<table class="widefat" style="border: none;">
						<thead>
							<tr>
								<th style="width: 40px; padding: 8px;"></th>
								<th style="padding: 8px;"><?php esc_html_e( 'Table Name', 'shopglut' ); ?></th>
								<th style="padding: 8px;"><?php esc_html_e( 'Description', 'shopglut' ); ?></th>
								<th style="padding: 8px; width: 100px;"><?php esc_html_e( 'Status', 'shopglut' ); ?></th>
								<th style="padding: 8px; width: 80px;"><?php esc_html_e( 'Records', 'shopglut' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $tables_status as $key => $data ) : ?>
								<tr>
									<td style="padding: 8px;">
										<?php if ( $data['exists'] ) : ?>
											<input type="checkbox" class="shopglut-table-checkbox" name="shopglut_tables[]" value="<?php echo esc_attr( $key ); ?>">
										<?php endif; ?>
									</td>
									<td style="padding: 8px;">
										<code><?php echo esc_html( $data['info']['name'] ); ?></code>
									</td>
									<td style="padding: 8px; color: #666;">
										<?php echo esc_html( $data['info']['description'] ); ?>
									</td>
									<td style="padding: 8px;">
										<?php if ( $data['exists'] ) : ?>
											<span style="color: #46b450;">● <?php esc_html_e( 'Exists', 'shopglut' ); ?></span>
										<?php else : ?>
											<span style="color: #999;">○ <?php esc_html_e( 'Not Created', 'shopglut' ); ?></span>
										<?php endif; ?>
									</td>
									<td style="padding: 8px; text-align: center;">
										<?php if ( $data['exists'] ) : ?>
											<span style="background: #e5e5e5; padding: 2px 8px; border-radius: 10px; font-size: 12px;">
												<?php echo esc_html( $data['row_count'] ); ?>
											</span>
										<?php else : ?>
											<span style="color: #999;">—</span>
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>

				<!-- Action Buttons -->
				<div class="shopglut-action-buttons" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
					<button type="button" id="shopglut-delete-selected-tables" class="button" style="background: #dc3545; color: #fff; border-color: #dc3545; padding: 8px 16px;">
						<span class="dashicons dashicons-trash" style="margin-top: 3px;"></span>
						<?php esc_html_e( 'Delete Selected Tables', 'shopglut' ); ?>
					</button>
					<button type="button" id="shopglut-delete-all-tables" class="button button-link-delete" style="padding: 8px 16px;">
						<span class="dashicons dashicons-warning" style="margin-top: 3px;"></span>
						<?php esc_html_e( 'Delete ALL Tables', 'shopglut' ); ?>
					</button>
					<button type="button" id="shopglut-reset-modules" class="button button-secondary" style="padding: 8px 16px;">
						<span class="dashicons dashicons-update" style="margin-top: 3px;"></span>
						<?php esc_html_e( 'Reset All (Disable Modules + Clear Data)', 'shopglut' ); ?>
					</button>
					<span class="spinner" id="shopglut-delete-spinner" style="float: none; margin-left: 10px;"></span>
				</div>

				<p class="description" style="margin-top: 10px; color: #dc3545;">
					<strong><?php esc_html_e( 'Warning: Deleting tables is irreversible. Always backup your database first!', 'shopglut' ); ?></strong>
				</p>
				<p class="description" style="margin-top: 5px; color: #666;">
					<?php esc_html_e( 'Reset: Disables all modules and clears data from tables (keeps table structure).', 'shopglut' ); ?>
				</p>
			</div>

			<!-- Confirmation Modal -->
			<div id="shopglut-delete-modal" style="display: none;">
				<div class="shopglut-modal-overlay" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); z-index: 100000;"></div>
				<div class="shopglut-modal-content" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: #fff; padding: 30px; border-radius: 8px; max-width: 500px; width: 90%; z-index: 100001; box-shadow: 0 5px 30px rgba(0,0,0,0.3);">
					<h2 style="margin-top: 0; color: #dc3545;">
						<span class="dashicons dashicons-warning" style="font-size: 24px; margin-right: 10px;"></span>
						<span id="shopglut-modal-title"><?php esc_html_e( 'Confirm Deletion', 'shopglut' ); ?></span>
					</h2>
					<p id="shopglut-modal-message" style="font-size: 14px; line-height: 1.6;"></p>
					<div id="shopglut-modal-tables" style="max-height: 200px; overflow-y: auto; margin: 15px 0; padding: 10px; background: #f9f9f9; border-radius: 4px;"></div>
					<p style="font-weight: bold; color: #dc3545; background: #fff3cd; padding: 10px; border-radius: 4px;">
						<?php esc_html_e( 'This action is IRREVERSIBLE. Please confirm you have a backup.', 'shopglut' ); ?>
					</p>
					<div style="margin-top: 20px; display: flex; gap: 10px; justify-content: flex-end;">
						<button type="button" id="shopglut-cancel-delete" class="button button-secondary">
							<?php esc_html_e( 'Cancel', 'shopglut' ); ?>
						</button>
						<button type="button" id="shopglut-confirm-delete" class="button" style="background: #dc3545; color: #fff; border-color: #dc3545;">
							<span class="dashicons dashicons-trash" style="margin-top: 3px;"></span>
							<span id="shopglut-confirm-btn-text"><?php esc_html_e( 'Yes, Delete', 'shopglut' ); ?></span>
						</button>
					</div>
				</div>
			</div>

			<script type="text/javascript">
			jQuery(document).ready(function($) {
				var selectedTables = [];
				var deleteAll = false;

				// Select/Deselect all checkboxes
				$('#shopglut-select-all-tables').on('change', function() {
					$('.shopglut-table-checkbox').prop('checked', $(this).prop('checked'));
				});

				// Update "Select All" when individual checkboxes change
				$(document).on('change', '.shopglut-table-checkbox', function() {
					var total = $('.shopglut-table-checkbox').length;
					var checked = $('.shopglut-table-checkbox:checked').length;
					$('#shopglut-select-all-tables').prop('checked', total === checked);
				});

				// Delete selected tables
				$('#shopglut-delete-selected-tables').on('click', function() {
					selectedTables = [];
					$('.shopglut-table-checkbox:checked').each(function() {
						selectedTables.push($(this).val());
					});

					if (selectedTables.length === 0) {
						alert('<?php echo esc_js( __( 'Please select at least one table to delete.', 'shopglut' ) ); ?>');
						return;
					}

					deleteAll = false;
					showModal(selectedTables);
				});

				// Delete all tables
				$('#shopglut-delete-all-tables').on('click', function() {
					var allTables = [];
					$('.shopglut-table-checkbox').each(function() {
						allTables.push($(this).val());
					});

					if (allTables.length === 0) {
						alert('<?php echo esc_js( __( 'No tables exist to delete.', 'shopglut' ) ); ?>');
						return;
					}

					deleteAll = true;
					selectedTables = allTables;
					showModal(allTables, true);
				});

				// Reset module states
				$('#shopglut-reset-modules').on('click', function() {
					if (!confirm('<?php echo esc_js( __( 'This will disable all modules and clear data from all tables (tables will remain). Continue?', 'shopglut' ) ); ?>')) {
						return;
					}

					var $btn = $(this);
					var $spinner = $('#shopglut-delete-spinner');

					$btn.prop('disabled', true);
					$spinner.addClass('is-active');

					$.ajax({
						url: ajaxurl,
						method: 'POST',
						data: {
							action: 'shopglut_reset_module_states',
							nonce: '<?php echo esc_attr( wp_create_nonce( 'shopglut_data_management' ) ); ?>'
						},
						success: function(response) {
							$spinner.removeClass('is-active');
							$btn.prop('disabled', false);

							if (response.success) {
								alert('✅ ' + response.data.message);
								location.reload();
							} else {
								alert('❌ ' + response.data.message);
							}
						},
						error: function() {
							$spinner.removeClass('is-active');
							$btn.prop('disabled', false);
							alert('❌ <?php echo esc_js( __( 'An error occurred. Please try again.', 'shopglut' ) ); ?>');
						}
					});
				});

				// Show modal with selected tables
				function showModal(tables, isAll) {
					var $modalTables = $('#shopglut-modal-tables');
					$modalTables.empty();

					tables.forEach(function(key) {
						var name = $('input[value="' + key + '"]').closest('tr').find('code').text();
						$modalTables.append('<div style="padding: 5px 0; border-bottom: 1px solid #eee;"><span class="dashicons dashicons-database" style="color: #666; margin-right: 5px;"></span>' + name + '</div>');
					});

					if (isAll) {
						$('#shopglut-modal-title').text('<?php echo esc_js( __( 'Delete ALL Tables', 'shopglut' ) ); ?>');
						$('#shopglut-modal-message').text('<?php echo esc_js( __( 'You are about to permanently delete ALL existing ShopGlut database tables:', 'shopglut' ) ); ?>');
						$('#shopglut-confirm-btn-text').text('<?php echo esc_js( __( 'Yes, Delete ALL Tables', 'shopglut' ) ); ?>');
					} else {
						$('#shopglut-modal-title').text('<?php echo esc_js( __( 'Delete Selected Tables', 'shopglut' ) ); ?>');
						$('#shopglut-modal-message').text('<?php echo esc_js( __( 'You are about to permanently delete the following tables:', 'shopglut' ) ); ?>');
						$('#shopglut-confirm-btn-text').text('<?php echo esc_js( __( 'Yes, Delete Selected', 'shopglut' ) ); ?>');
					}

					$('#shopglut-delete-modal').show();
				}

				// Hide modal on cancel
				$('#shopglut-cancel-delete, .shopglut-modal-overlay').on('click', function() {
					$('#shopglut-delete-modal').hide();
				});

				// Confirm delete
				$('#shopglut-confirm-delete').on('click', function() {
					var $btn = $(this);
					var $spinner = $('#shopglut-delete-spinner');

					$btn.prop('disabled', true);
					$spinner.addClass('is-active');

					$.ajax({
						url: ajaxurl,
						method: 'POST',
						data: {
							action: 'shopglut_delete_selected_tables',
							tables: selectedTables,
							nonce: '<?php echo esc_attr( wp_create_nonce( 'shopglut_data_management' ) ); ?>'
						},
						success: function(response) {
							$spinner.removeClass('is-active');
							$btn.prop('disabled', false);
							$('#shopglut-delete-modal').hide();

							if (response.success) {
								alert('✅ ' + response.data.message);
								location.reload();
							} else {
								alert('❌ ' + response.data.message);
							}
						},
						error: function() {
							$spinner.removeClass('is-active');
							$btn.prop('disabled', false);
							alert('❌ <?php echo esc_js( __( 'An error occurred. Please try again.', 'shopglut' ) ); ?>');
						}
					});
				});
			});
			</script>
			<?php
			echo wp_kses_post( $this->field_after() );
		}
	}
}
