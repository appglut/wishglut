<?php
namespace Wishglut;

trait WishlistBulkActions {
    
    private function render_wishlist_bulkAction() {

        ob_start(); ?>

        <!-- Bulk Actions Table Row -->
        <tr class="wishglut-wishlist-bulk-actions-row">
            <td colspan="9" class="wishglut-wishlist-bulk-actions-container">
                <div class="wishglut-wishlist-bulk-actions">
                    <div class="wishglut-to-left look_in">
                        <div class="wishglut-input-group wishglut-no-full">
                            <input type="hidden" name="lists_per_page" value="10" id="wishglut_lists_per_page">
                            <select name="product_actions" id="wishglut_product_actions" class="wishglut-break-input-filed form-control">
                                <option value="" selected="selected"><?php echo esc_html__('Actions', 'wishglut'); ?></option>
                                <option value="add_to_cart_selected"><?php echo esc_html__('Add to Cart', 'wishglut'); ?></option>
                                <option value="remove_selected"><?php echo esc_html__('Remove', 'wishglut'); ?></option>
                            </select>
                            <span class="wishglut-input-group-btn">
                                <button type="submit" class="button" name="wishglut-action-product_apply" value="product_apply" title="<?php echo esc_attr__('Apply Action', 'wishglut'); ?>" id="wishglut-apply-action">
                                    <?php echo esc_html__('Apply', 'wishglut'); ?> <span class="wishglut-mobile"><?php echo esc_html__('Action', 'wishglut'); ?></span>
                                </button>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Original buttons (you can keep these as alternatives or remove them) -->
                    <div class="original-bulk-buttons" style="margin-top: 10px;">
                        <button class="btn-add-selected-cart" id="add-selected-to-cart" disabled><?php echo esc_html__('Add Selected to Cart', 'wishglut'); ?></button>
                        <button class="btn-add-all-cart" id="add-all-to-cart"><?php echo esc_html__('Add All to Cart', 'wishglut'); ?></button>
                    </div>
                </div>
            </td>
        </tr>

        <?php 

        return ob_get_clean();
    }
}