<?php
namespace Wishglut;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


use AGWISHGLUT;
use AGWISHGLUT_Options;

class WishlistMenuHandler {

    private $agwishglut_instance = null;
    private $menu_slug = 'shopglut_wishlist'; // Default menu slug (Wishglut submenu)

    /**
     * Constructor - optionally accepts a custom menu slug for individual menu
     */
    public function __construct($menu_slug = null) {
        // Set custom menu slug if provided
        if ($menu_slug) {
            $this->menu_slug = $menu_slug;
        }

        add_action('admin_enqueue_scripts', array($this, 'enqueueAssets'));
        
        // Hook into AGWISHGLUT initialization - try multiple hooks
        add_action('agl_loaded', array($this, 'getAGWISHGLUTInstance'));
        add_action('init', array($this, 'getAGWISHGLUTInstance'), 999); // Late init
        add_action('admin_init', array($this, 'getAGWISHGLUTInstance'), 999);
        
        // Also try to get instance when admin page loads
        add_action('current_screen', array($this, 'getAGWISHGLUTInstance'));
    }

    /**
     * Get AGWISHGLUT instance after it's loaded - Multiple approaches
     */
    public function getAGWISHGLUTInstance() {
        // Skip if already found
        if ($this->agwishglut_instance) {
            return;
        }

        // Method 1: Check AGWISHGLUT_Options static instances
        if (class_exists('AGWISHGLUT_Options')) {
            // Check if the static instances property exists
            if (isset(AGWISHGLUT_Options::$instances) && is_array(AGWISHGLUT_Options::$instances)) {
                foreach (AGWISHGLUT_Options::$instances as $key => $instance) {
                    if ($key === 'agwishglut_wishlist_options') {
                        $this->agwishglut_instance = $instance;
                        return;
                    }
                }
            }
        }

        // Method 2: Try to get from AGWISHGLUT main class instances
        if (class_exists('AGWISHGLUT') && isset(AGWISHGLUT::$inited)) {
            if (isset(AGWISHGLUT::$inited['agwishglut_wishlist_options'])) {
                // Try to recreate or find the instance
                $this->tryRecreateInstance();
            }
        }

        // Method 3: Check global variables
        global $agwishglut_wishlist_options;
        if (isset($agwishglut_wishlist_options) && is_object($agwishglut_wishlist_options)) {
            $this->agwishglut_instance = $agwishglut_wishlist_options;
            return;
        }

        // Method 4: Try to find any AGWISHGLUT_Options instance and check its unique property
        if (class_exists('AGWISHGLUT_Options')) {
            // Get all defined variables and look for AGWISHGLUT_Options instances
            $this->findInstanceInGlobals();
        }
    }

    /**
     * Try to recreate instance from stored data
     */
    private function tryRecreateInstance() {
        if (!class_exists('AGWISHGLUT_Options')) {
            return;
        }

        // Get the stored sections from AGWISHGLUT
        if (isset(AGWISHGLUT::$args['sections']['agwishglut_wishlist_options'])) {
            $sections = AGWISHGLUT::$args['sections']['agwishglut_wishlist_options'];
            $args = isset(AGWISHGLUT::$args['admin_options']['agwishglut_wishlist_options']) ? 
                    AGWISHGLUT::$args['admin_options']['agwishglut_wishlist_options'] : array();

            try {
                $this->agwishglut_instance = new AGWISHGLUT_Options('agwishglut_wishlist_options', array(
                    'args' => $args,
                    'sections' => $sections
                ));
            } catch (Exception $e) {
                // Handle silently
            }
        }
    }

    /**
     * Find instance in global variables
     */
    private function findInstanceInGlobals() {
        // Check all global variables for AGWISHGLUT_Options instances
        foreach ($GLOBALS as $var_name => $var_value) {
            if (is_object($var_value) && 
                $var_value instanceof AGWISHGLUT_Options && 
                isset($var_value->unique) && 
                $var_value->unique === 'agwishglut_wishlist_options') {
                $this->agwishglut_instance = $var_value;
                return;
            }
        }
    }

    /**
     * Get instance with fallback enhancements
     */
    private function getInstanceWithFallback() {
        if ($this->agwishglut_instance) {
            return $this->agwishglut_instance;
        }

        // Try one more time to get the instance
        $this->getAGWISHGLUTInstance();

        if ($this->agwishglut_instance) {
            return $this->agwishglut_instance;
        }

        // Final fallback - try to access enhancements directly
        return $this->createFallbackInstance();
    }

    /**
     * Create fallback instance with basic functionality
     */
    private function createFallbackInstance() {
        // Get options directly from database
        $options = get_option('agwishglut_wishlist_options', array());
        
        if (empty($options)) {
            return null;
        }

        // Create a simple object to hold the options
        $fallback = new \stdClass();
        $fallback->options = $options;
        $fallback->unique = 'agwishglut_wishlist_options';
        $fallback->pre_sections = array(); // Will be empty but prevents errors
        
        return $fallback;
    }

    /**
     * Main render method - displays the complete wishlist interface
     */
   public function render() {
    // Check if wishlist module is disabled (only if ModuleManager exists)
    if (class_exists('\Wishglut\ModuleManager')) {
        $module_manager = \Wishglut\ModuleManager::get_instance();
        if ($module_manager->should_show_disabled_message('wishlist')) {
            $module_manager->render_disabled_module_message('wishlist');
            return;
        }
    }
    
    // Get current tab from URL parameter
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $current_tab = isset($_GET['tab']) ? sanitize_text_field(wp_unslash($_GET['tab'])) : 'dashboard';
    
    // Check if pro version is active
    $is_pro = $this->isProVersionActive();
 
    ?>
    <div class="wrap wishglut-wishlist-wrapper">
        <!-- Wishlist Tab Navigation -->
        <nav class="nav-tab-wrapper wp-clearfix" aria-label="Secondary menu">
            <a href="<?php echo esc_url( $this->getTabUrl( 'dashboard' ) ); ?>" 
               class="nav-tab <?php echo $current_tab === 'dashboard' ? 'nav-tab-active' : ''; ?>">
                📊 <?php echo esc_html__('Dashboard', 'wishglut'); ?>
            </a>
            
            <a href="<?php echo esc_url( $this->getTabUrl( 'settings' ) ); ?>" 
               class="nav-tab <?php echo $current_tab === 'settings' ? 'nav-tab-active' : ''; ?>">
                ⚙️ <?php echo esc_html__('Settings', 'wishglut'); ?>
            </a>

            <!-- Pro tabs - always visible but with different styling -->
            <!-- <a href="<?php echo esc_url( $this->getTabUrl( 'analytics' ) ); ?>" 
               class="nav-tab <?php echo $current_tab === 'analytics' ? 'nav-tab-active' : ''; ?> <?php echo !$is_pro ? 'nav-tab-pro-locked' : ''; ?>">
                📈 <?php echo esc_html__('Analytics', 'wishglut'); ?>
                <?php if (!$is_pro): ?><span class="pro-badge">PRO</span><?php endif; ?>
            </a>
            
           
            
            <a href="<?php echo esc_url( $this->getTabUrl( 'integrations' ) ); ?>" 
               class="nav-tab <?php echo $current_tab === 'integrations' ? 'nav-tab-active' : ''; ?> <?php echo !$is_pro ? 'nav-tab-pro-locked' : ''; ?>">
                🔌 <?php echo esc_html__('Integrations', 'wishglut'); ?>
                <?php if (!$is_pro): ?><span class="pro-badge">PRO</span><?php endif; ?>
            </a> -->

             <a href="<?php echo esc_url( $this->getTabUrl( 'users' ) ); ?>" 
            class="nav-tab <?php echo $current_tab === 'users' ? 'nav-tab-active' : ''; ?> <?php echo !$is_pro ? 'nav-tab-pro-locked' : ''; ?>">
                📧 <?php echo esc_html__('Email Management', 'wishglut'); ?>
                <?php if (!$is_pro): ?><span class="pro-badge">PRO</span><?php endif; ?>
          </a>
            
            <!-- Help & Support - always available -->
            <a href="<?php echo esc_url( $this->getTabUrl( 'support' ) ); ?>" 
               class="nav-tab <?php echo $current_tab === 'support' ? 'nav-tab-active' : ''; ?>">
                🆘 <?php echo esc_html__('Help & Support', 'wishglut'); ?>
            </a>
            
            <?php if (!$is_pro): ?>
                <!-- Upgrade button for non-pro users -->
                <a href="<?php echo esc_url( $this->getUpgradeUrl() ); ?>" 
                   class="nav-tab nav-tab-upgrade" 
                   target="_blank">
                    ⭐ <?php echo esc_html__('Upgrade to Pro', 'wishglut'); ?>
                </a>
            <?php endif; ?>
        </nav>

        <!-- Tab Content -->
        <div class="wishglut-wishlist-settings tab-content-wrapper">
            <?php
            switch ($current_tab) {
                case 'dashboard':
                    $this->renderDashboardTab();
                    break;
                case 'settings':
                    $this->renderSettingsTab();
                    break;
                    
                case 'analytics':
                    if ($is_pro) {
                        $this->renderAnalyticsTab();
                    } else {
                        $this->renderProTabPreview('analytics');
                    }
                    break;
                    
                case 'users':
                    if ($is_pro) {
                        $this->renderUsersTab();
                    } else {
                        $this->renderProTabPreview('users');
                    }
                    break;
                    
                case 'integrations':
                    if ($is_pro) {
                        $this->renderIntegrationsTab();
                    } else {
                        $this->renderProTabPreview('integrations');
                    }
                    break;
                    
                case 'support':
                    // Help & Support is always available in free version
                    $this->renderSupportTab();
                    break;
                    
                default:
                    $this->renderDashboardTab();
            }
            ?>
        </div>
    </div>

    <?php $this->renderStyles(); ?>
    <?php
}

/**
 * Check if pro version is active
 */
private function isProVersionActive() {

    
    // Option 1: Check if pro plugin is active
    if (is_plugin_active('wishglut-wishlist-pro/wishglut-wishlist-pro.php')) {
        
        return true;
    }
    
    // Option 2: Check for license key
    // $license_key = get_option('wishglut_wishlist_license_key');
    // if (!empty($license_key) && $this->validateLicense($license_key)) {
    //     return true;
    // }
    
    // Option 3: Check for pro constant
    if (defined('WISHGLUT_WISHLIST_PRO') && WISHGLUT_WISHLIST_PRO === true) {
        return true;
    }
    
    return false;
}

/**
 * Validate license key (implement your own logic)
 */
private function validateLicense($license_key) {
    // Implement your license validation logic here
    // This could involve API calls to your server
    return false; // Placeholder
}

/**
 * Get upgrade URL
 */
private function getUpgradeUrl() {
    return 'https://www.appglut.com/wishglut-wishlist/'; // Replace with your actual upgrade URL
}


/**
 * Render Analytics Tab - Pro Feature Placeholder
 */
public function renderAnalyticsTab() {
    // Check if pro version extends this
    if (has_action('wishglut_render_analytics_tab')) {
        do_action('wishglut_render_analytics_tab');
        return;
    }
    
    // Default free version preview
    $this->renderProTabPreview('analytics');
}

/**
 * Render Users Tab - Pro Feature Placeholder
 */
public function renderUsersTab() {
    // Check if pro version extends this
    if (has_action('wishglut_render_users_tab')) {
        do_action('wishglut_render_users_tab');
        return;
    }
    
    // Default free version preview
    $this->renderProTabPreview('users');
}

/**
 * Render Integrations Tab - Pro Feature Placeholder
 */
public function renderIntegrationsTab() {
    // Check if pro version extends this
    if (has_action('wishglut_render_integrations_tab')) {
        do_action('wishglut_render_integrations_tab');
        return;
    }
    
    // Default free version preview
    $this->renderProTabPreview('integrations');
}

/**
 * Render pro tab preview with content and upgrade overlay
 */
private function renderProTabPreview($tab_name) {
    // Allow pro plugin to completely override this function
    if (has_action("wishglut_render_{$tab_name}_tab")) {
        do_action("wishglut_render_{$tab_name}_tab");
        return;
    }
    
    // Hook before tab preview rendering
    do_action('wishglut_before_pro_tab_preview', $tab_name);
    
    $tab_configs = [
        'analytics' => [
            'title' => __('Analytics Dashboard', 'wishglut'),
            'description' => __('Get detailed insights into your wishlist performance, user behavior, and conversion rates.', 'wishglut'),
            'content' => $this->getAnalyticsPreviewContent()
        ],
        'users' => [
            'title' => __('Email Management', 'wishglut'),
            'description' => __('Manage wishlist users mail, view their activity, and understand user engagement patterns.', 'wishglut'),
            'content' => $this->getUsersPreviewContent()
        ],
        'integrations' => [
            'title' => __('Third-party Integrations', 'wishglut'),
            'description' => __('Connect your wishlist with popular email marketing, CRM, and analytics platforms.', 'wishglut'),
            'content' => $this->getIntegrationsPreviewContent()
        ]
    ];
    
    // Allow filtering of tab configs
    $tab_configs = apply_filters('wishglut_pro_tab_configs', $tab_configs, $tab_name);
    
    $config = isset($tab_configs[$tab_name]) ? $tab_configs[$tab_name] : $tab_configs['analytics'];
    
    // Allow filtering of individual tab config
    $config = apply_filters("wishglut_pro_tab_config_{$tab_name}", $config);
    ?>
    <div class="wishglut-pro-tab-preview">
        <?php do_action('wishglut_pro_tab_preview_start', $tab_name, $config); ?>
        
        <!-- Pro Content Preview -->
        <div class="pro-content-preview">
            <?php 
            // Hook to modify preview content
            do_action("wishglut_before_{$tab_name}_preview_content");
            echo wp_kses_post( $config['content'] ); 
            do_action("wishglut_after_{$tab_name}_preview_content");
            ?>
        </div>
        
        <!-- Pro Upgrade Overlay -->
        <div class="pro-upgrade-overlay">
            <?php do_action('wishglut_before_upgrade_overlay', $tab_name); ?>
            
            <div class="pro-upgrade-content">
                <?php do_action('wishglut_upgrade_content_start', $tab_name); ?>
                
                <div class="pro-upgrade-icon">⭐</div>
                <h2><?php echo esc_html( $config['title'] ); ?> - <?php echo esc_html__('Pro Feature', 'wishglut'); ?></h2>
                <p><?php echo esc_html( $config['description'] ); ?></p>
                
                <?php do_action('wishglut_upgrade_content_middle', $tab_name, $config); ?>
                
                <div class="pro-upgrade-actions">
                    <?php do_action('wishglut_before_upgrade_actions', $tab_name); ?>
                    
                    <a href="<?php echo esc_url( $this->getUpgradeUrl() ); ?>" 
                       class="button button-primary button-hero" 
                       target="_blank">
                        <?php echo esc_html__('Unlock This Feature', 'wishglut'); ?>
                    </a>
                    
                    <?php do_action('wishglut_after_upgrade_actions', $tab_name); ?>
                </div>
                
                <?php do_action('wishglut_upgrade_content_end', $tab_name); ?>
            </div>
            
            <?php do_action('wishglut_after_upgrade_overlay', $tab_name); ?>
        </div>
        
        <?php do_action('wishglut_pro_tab_preview_end', $tab_name, $config); ?>
    </div>
    
    <?php
    // Hook for custom styles
    do_action('wishglut_pro_tab_preview_styles', $tab_name);
    
    // Default styles (can be overridden by pro plugin)
    if (!has_action('wishglut_pro_tab_preview_styles')) {
        $this->renderDefaultProTabStyles();
    }
    
    // Hook after tab preview rendering
    do_action('wishglut_after_pro_tab_preview', $tab_name);
}

/**
 * Render default pro tab styles (can be overridden)
 */
private function renderDefaultProTabStyles() {
    // Allow pro plugin to completely override styles
    if (has_filter('wishglut_pro_tab_custom_styles')) {
        echo esc_html( wp_strip_all_tags( apply_filters( 'wishglut_pro_tab_custom_styles', '' ) ) );
        return;
    }
    ?>
    <style>
   
    
    <?php echo esc_html( wp_strip_all_tags( apply_filters( 'wishglut_pro_tab_additional_styles', '' ) ) ); ?>
    </style>
    <?php
}


/**
 * Get analytics preview content
 */
private function getAnalyticsPreviewContent() {
    ob_start();
    ?>
    <div class="analytics-preview">
        <h2><?php echo esc_html__('Analytics Dashboard', 'wishglut'); ?></h2>
        
        <div class="analytics-cards">
            <div class="analytics-card">
                <h3><?php echo esc_html__('Total Wishlists', 'wishglut'); ?></h3>
                <div class="analytics-number">1,234</div>
                <div class="analytics-change positive">+12% this month</div>
            </div>
            
            <div class="analytics-card">
                <h3><?php echo esc_html__('Conversion Rate', 'wishglut'); ?></h3>
                <div class="analytics-number">24.5%</div>
                <div class="analytics-change positive">+3.2% this month</div>
            </div>
            
            <div class="analytics-card">
                <h3><?php echo esc_html__('Active Users', 'wishglut'); ?></h3>
                <div class="analytics-number">856</div>
                <div class="analytics-change negative">-2% this month</div>
            </div>
        </div>
        
        <div class="analytics-charts">
            <div class="chart-placeholder">
                <h4><?php echo esc_html__('Wishlist Activity Over Time', 'wishglut'); ?></h4>
                <div class="chart-mock"></div>
            </div>
            
            <div class="chart-placeholder">
                <h4><?php echo esc_html__('Top Wishlist Products', 'wishglut'); ?></h4>
                <div class="chart-mock"></div>
            </div>
        </div>
    </div>
    
    <style>
    .analytics-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin: 20px 0;
    }
    
    .analytics-card {
        background: white;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #ddd;
        text-align: center;
    }
    
    .analytics-number {
        font-size: 36px;
        font-weight: bold;
        color: #333;
        margin: 10px 0;
    }
    
    .analytics-change.positive {
        color: #28a745;
    }
    
    .analytics-change.negative {
        color: #dc3545;
    }
    
    .analytics-charts {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-top: 30px;
    }
    
    .chart-placeholder {
        background: white;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #ddd;
        min-height: 300px;
    }
    
    .chart-mock {
        background: linear-gradient(45deg, #f0f0f0 25%, transparent 25%, transparent 75%, #f0f0f0 75%),
                    linear-gradient(45deg, #f0f0f0 25%, transparent 25%, transparent 75%, #f0f0f0 75%);
        background-size: 20px 20px;
        background-position: 0 0, 10px 10px;
        height: 200px;
        border-radius: 4px;
        margin-top: 15px;
    }
    </style>
    <?php
    return ob_get_clean();
}

/**
 * Get users preview content
 */
private function getUsersPreviewContent() {
    ob_start();
    ?>
    <div class="users-preview">
        <h2><?php echo esc_html__('User Management', 'wishglut'); ?></h2>
        
        <div class="users-table-container">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php echo esc_html__('User', 'wishglut'); ?></th>
                        <th><?php echo esc_html__('Items in Wishlist', 'wishglut'); ?></th>
                        <th><?php echo esc_html__('Last Activity', 'wishglut'); ?></th>
                        <th><?php echo esc_html__('Total Purchases', 'wishglut'); ?></th>
                        <th><?php echo esc_html__('Actions', 'wishglut'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>John Doe</strong><br>john@example.com</td>
                        <td>12 items</td>
                        <td>2 hours ago</td>
                        <td>$245.80</td>
                        <td><button class="button">View Details</button></td>
                    </tr>
                    <tr>
                        <td><strong>Jane Smith</strong><br>jane@example.com</td>
                        <td>8 items</td>
                        <td>1 day ago</td>
                        <td>$156.20</td>
                        <td><button class="button">View Details</button></td>
                    </tr>
                    <tr>
                        <td><strong>Mike Johnson</strong><br>mike@example.com</td>
                        <td>15 items</td>
                        <td>3 days ago</td>
                        <td>$398.50</td>
                        <td><button class="button">View Details</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Get integrations preview content
 */
private function getIntegrationsPreviewContent() {
    ob_start();
    ?>
    <div class="integrations-preview">
        <h2><?php echo esc_html__('Available Integrations', 'wishglut'); ?></h2>
        
        <div class="integrations-grid">
            <div class="integration-card">
                <div class="integration-logo">📧</div>
                <h3>Mailchimp</h3>
                <p>Sync wishlist data with your email campaigns</p>
                <button class="button button-primary">Connect</button>
            </div>
            
            <div class="integration-card">
                <div class="integration-logo">📊</div>
                <h3>Google Analytics</h3>
                <p>Track wishlist events in your analytics</p>
                <button class="button button-primary">Connect</button>
            </div>
            
            <div class="integration-card">
                <div class="integration-logo">🔗</div>
                <h3>Zapier</h3>
                <p>Connect with 1000+ apps via Zapier</p>
                <button class="button button-primary">Connect</button>
            </div>
            
            <div class="integration-card">
                <div class="integration-logo">💬</div>
                <h3>Slack</h3>
                <p>Get wishlist notifications in Slack</p>
                <button class="button button-primary">Connect</button>
            </div>
        </div>
    </div>
    
    <style>
    .integrations-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }
    
    .integration-card {
        background: white;
        padding: 30px;
        border-radius: 8px;
        border: 1px solid #ddd;
        text-align: center;
    }
    
    .integration-logo {
        font-size: 48px;
        margin-bottom: 15px;
    }
    
    .integration-card h3 {
        margin: 15px 0 10px 0;
        color: #333;
    }
    
    .integration-card p {
        color: #666;
        margin-bottom: 20px;
    }
    </style>
    <?php
    return ob_get_clean();
}

/**
 * Render Support Tab - Always available in free version
 */
private function renderSupportTab() {
    ?>
    <div class="wishglut-support-tab">
        <div class="support-header">
            <h2><?php echo esc_html__('Help & Support', 'wishglut'); ?></h2>
            <p><?php echo esc_html__('Get help with Wishglut Wishlist plugin. Find answers, contact support, and access documentation.', 'wishglut'); ?></p>
        </div>

        <div class="support-content">
            <div class="support-grid">
                <!-- Documentation Section -->
                <div class="support-card">
                    <div class="support-icon">📚</div>
                    <h3><?php echo esc_html__('Documentation', 'wishglut'); ?></h3>
                    <p><?php echo esc_html__('Browse our comprehensive documentation to get started quickly.', 'wishglut'); ?></p>
                    <a href="https://www.documentation.appglut.com/wishglut-wishlist/" target="_blank" class="button button-primary">
                        <?php echo esc_html__('View Documentation', 'wishglut'); ?>
                    </a>
                </div>

               

                <!-- Contact Support -->
                <div class="support-card">
                    <div class="support-icon">💬</div>
                    <h3><?php echo esc_html__('Contact Support', 'wishglut'); ?></h3>
                    <p><?php echo esc_html__('Need help? Our support team is here to assist you.', 'wishglut'); ?></p>
                    <a href="https://www.appglut.com/support" target="_blank" class="button button-primary">
                        <?php echo esc_html__('Contact Us', 'wishglut'); ?>
                    </a>
                </div>

                <!-- Feature Requests -->
                <div class="support-card">
                    <div class="support-icon">💡</div>
                    <h3><?php echo esc_html__('Feature Requests', 'wishglut'); ?></h3>
                    <p><?php echo esc_html__('Have an idea? Submit feature requests and suggestions.', 'wishglut'); ?></p>
                    <a href="https://www.appglut.com/support/forum/plugin-feature-request/" target="_blank" class="button button-primary">
                        <?php echo esc_html__('Submit Request', 'wishglut'); ?>
                    </a>
                </div>
            </div>


            <!-- System Information -->
            <div class="system-info-section">
                <h3><?php echo esc_html__('System Information', 'wishglut'); ?></h3>
                <div class="system-info-grid">
                    <div class="info-item">
                        <strong><?php echo esc_html__('Plugin Version:', 'wishglut'); ?></strong>
                        <span><?php echo defined('WISHGLUT_VERSION') ? esc_html( WISHGLUT_VERSION ) : '1.0.0'; ?></span>
                    </div>
                    <div class="info-item">
                        <strong><?php echo esc_html__('WordPress Version:', 'wishglut'); ?></strong>
                        <span><?php echo esc_html( get_bloginfo( 'version' ) ); ?></span>
                    </div>
                    <div class="info-item">
                        <strong><?php echo esc_html__('WooCommerce Version:', 'wishglut'); ?></strong>
                        <span><?php echo defined('WC_VERSION') ? esc_html( WC_VERSION ) : esc_html__('Not Installed', 'wishglut'); ?></span>
                    </div>
                    <div class="info-item">
                        <strong><?php echo esc_html__('PHP Version:', 'wishglut'); ?></strong>
                        <span><?php echo esc_html( PHP_VERSION ); ?></span>
                    </div>
                </div>
                
               
            </div>
        </div>
    </div>

<style>
    .wishglut-support-tab {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #ddd;
    }

    .support-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .support-header h2 {
        color: #333;
        margin-bottom: 10px;
    }

    .support-header p {
        color: #666;
        font-size: 16px;
    }

    .support-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 40px;
    }

    .support-card {
        background: #f9f9f9;
        padding: 30px;
        border-radius: 8px;
        text-align: center;
        border: 1px solid #eee;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .support-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    }

    .support-icon {
        font-size: 48px;
        margin-bottom: 20px;
    }

    .support-card h3 {
        color: #333;
        margin-bottom: 15px;
    }

    .support-card p {
        color: #666;
        margin-bottom: 20px;
        line-height: 1.5;
    }

    .quick-help-section, .system-info-section {
        background: #f9f9f9;
        padding: 30px;
        border-radius: 8px;
        margin-top: 30px;
    }

    .help-accordion {
        margin-top: 20px;
    }

    .help-item {
        border-bottom: 1px solid #eee;
        margin-bottom: 10px;
    }

    .help-question {
        width: 100%;
        background: none;
        border: none;
        padding: 15px 0;
        text-align: left;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 16px;
        font-weight: 500;
        color: #333;
    }

    .help-question:hover {
        color: #0073aa;
    }

    .help-toggle {
        font-size: 20px;
        font-weight: bold;
        transition: transform 0.3s ease;
    }

    .help-answer {
        display: none;
        padding: 0 0 20px 0;
        color: #666;
        line-height: 1.6;
    }

    .help-answer.show {
        display: block;
    }

    .help-item.active .help-toggle {
        transform: rotate(45deg);
    }

    .system-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 15px;
        margin: 20px 0;
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }

    .system-actions {
        margin-top: 20px;
    }

    .system-actions .button {
        margin-right: 10px;
    }
</style>

<script>
    function toggleHelp(button) {
        const item = button.closest('.help-item');
        const answer = item.querySelector('.help-answer');
        const isActive = item.classList.contains('active');
        
        // Close all other items
        document.querySelectorAll('.help-item').forEach(el => {
            el.classList.remove('active');
            el.querySelector('.help-answer').classList.remove('show');
        });
        
        // Toggle current item
        if (!isActive) {
            item.classList.add('active');
            answer.classList.add('show');
        }
    }

    function copySystemInfo() {
        const systemInfo = `
        Plugin Version: <?php echo defined('WISHGLUT_WISHLIST_VERSION') ? esc_html( WISHGLUT_WISHLIST_VERSION ) : '1.0.0'; ?>
        WordPress Version: <?php echo esc_html( get_bloginfo( 'version' ) ); ?>
        WooCommerce Version: <?php echo defined('WC_VERSION') ? esc_html( WC_VERSION ) : 'Not Installed'; ?>
        PHP Version: <?php echo esc_html( PHP_VERSION ); ?>
        Site URL: <?php echo esc_url( home_url() ); ?>
            `.trim();
        
        navigator.clipboard.writeText(systemInfo).then(function() {
            alert('<?php echo esc_js(__('System information copied to clipboard!', 'wishglut')); ?>');
        }).catch(function() {
            // Silently fail
        });
    }
    </script>
    <?php
}
    /**
     * Render Settings Tab using AGWISHGLUT
     */
    private function renderSettingsTab() {
        // Try to get instance
        $instance = $this->getInstanceWithFallback();
        
        if (!$instance) {
            ?>
            <div class="settings-content">
                <div class="notice notice-error">
                    <p><?php echo esc_html__('Settings are not available at this time. Please refresh the page.', 'wishglut'); ?></p>
                    <p>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=' . $this->menu_slug . '&tab=settings')); ?>" class="button button-primary">
                            <?php echo esc_html__('Refresh Page', 'wishglut'); ?>
                        </a>
                    </p>
                </div>
            </div>
            <?php
            return;
        }

        // Get current subtab
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $current_subtab = isset($_GET['subtab']) ? sanitize_text_field(wp_unslash($_GET['subtab'])) : '';

        ?>
        <div class="settings-content">
            <?php if ($current_subtab): ?>
                <div class="settings-nav">
                    <nav class="nav-tab-wrapper settings-nav-tabs">
                        <a href="<?php echo esc_url( $this->getTabUrl( 'settings', 'general' ) ); ?>" 
                           class="nav-tab <?php echo $current_subtab === 'general' ? 'nav-tab-active' : ''; ?>">
                            🔧 <?php echo esc_html__('General', 'wishglut'); ?>
                        </a>
                        <a href="<?php echo esc_url( $this->getTabUrl( 'settings', 'wishlist-page' ) ); ?>" 
                           class="nav-tab <?php echo $current_subtab === 'wishlist-page' ? 'nav-tab-active' : ''; ?>">
                            📋 <?php echo esc_html__('Wishlist Page', 'wishglut'); ?>
                        </a>
                        <a href="<?php echo esc_url( $this->getTabUrl( 'settings', 'account-page' ) ); ?>" 
                           class="nav-tab <?php echo $current_subtab === 'account-page' ? 'nav-tab-active' : ''; ?>">
                            👤 <?php echo esc_html__('Account Page', 'wishglut'); ?>
                        </a>
                        <a href="<?php echo esc_url( $this->getTabUrl( 'settings', 'product-page' ) ); ?>" 
                           class="nav-tab <?php echo $current_subtab === 'product-page' ? 'nav-tab-active' : ''; ?>">
                            🛍️ <?php echo esc_html__('Product Page', 'wishglut'); ?>
                        </a>
                        <a href="<?php echo esc_url( $this->getTabUrl( 'settings', 'shop-page' ) ); ?>" 
                           class="nav-tab <?php echo $current_subtab === 'shop-page' ? 'nav-tab-active' : ''; ?>">
                            🏪 <?php echo esc_html__('Shop Page', 'wishglut'); ?>
                        </a>
                        <a href="<?php echo esc_url( $this->getTabUrl( 'settings', 'archive-page' ) ); ?>" 
                           class="nav-tab <?php echo $current_subtab === 'archive-page' ? 'nav-tab-active' : ''; ?>">
                            📁 <?php echo esc_html__('Archive Page', 'wishglut'); ?>
                        </a>
                    </nav>
                </div>
            <?php endif; ?>

            <!-- Render AGWISHGLUT Settings -->
            <div class="agwishglut-settings-wrapper">
                <?php $this->renderAGWISHGLUTSettings($current_subtab, $instance); ?>
            </div>
        </div>
        
        <?php $this->renderSettingsStyles(); ?>
        <?php
    }

    /**
     * Render AGWISHGLUT Settings with optional filtering
     */
    private function renderAGWISHGLUTSettings($subtab = '', $instance = null) {
        if (!$instance) {
            echo '<div class="notice notice-error"><p>' . esc_html__('Settings instance not available.', 'wishglut') . '</p></div>';
            return;
        }

        // Check if this is a full AGWISHGLUT_Options instance
        if (method_exists($instance, 'add_options_html')) {
            if ($subtab) {
                $this->renderFilteredAGWISHGLUTSettings($subtab, $instance);
            } else {
                // Render all settings using AGWISHGLUT's native method
                $instance->add_options_html();
            }
        } else {
            // This is our fallback instance, show basic options
            $this->renderBasicOptionsForm($instance);
        }
    }

    /**
     * Render filtered AGWISHGLUT settings for specific subtab
     */
    private function renderFilteredAGWISHGLUTSettings($subtab, $instance) {
        // Get sections that match the subtab
        $filtered_sections = $this->getFilteredSections($subtab, $instance);
        
        if (empty($filtered_sections)) {
            echo '<div class="notice notice-info"><p>' . esc_html__('No settings available for this section.', 'wishglut') . '</p></div>';
            return;
        }

        // Render filtered settings
        $this->renderCustomAGWISHGLUTForm($filtered_sections, $instance);
    }

    /**
     * Filter sections based on subtab
     */
    private function getFilteredSections($subtab, $instance) {
        $all_sections = isset($instance->pre_sections) ? $instance->pre_sections : array();
        $filtered_sections = array();

        // Map subtabs to section titles
        $subtab_mapping = array(
            'general' => array('General'),
            'product-page' => array('Product Page'),
            'shop-page' => array('Shop Page'),
            'archive-page' => array('Archive Page'),
            'wishlist-page' => array('Wishlist Page'),
            'account-page' => array('Account Page')
        );

        if (!isset($subtab_mapping[$subtab])) {
            return $filtered_sections;
        }

        $target_titles = $subtab_mapping[$subtab];

        foreach ($all_sections as $section) {
            if (isset($section['title']) && in_array($section['title'], $target_titles)) {
                $filtered_sections[] = $section;
            }
        }

        return $filtered_sections;
    }

    /**
     * Render custom AGWISHGLUT form with filtered sections
     */
    private function renderCustomAGWISHGLUTForm($sections, $instance) {
        $has_nav = false; // No navigation for filtered view
        $show_all = ' agl-show-all';
        $ajax_class = (isset($instance->args['ajax_save']) && $instance->args['ajax_save']) ? ' agl-save-ajax' : '';
        $wrapper_class = (isset($instance->args['framework_class']) && $instance->args['framework_class']) ? ' ' . $instance->args['framework_class'] : '';
        $theme = (isset($instance->args['theme']) && $instance->args['theme']) ? ' agl-theme-' . $instance->args['theme'] : '';
        $form_action = (isset($instance->args['form_action']) && $instance->args['form_action']) ? $instance->args['form_action'] : '';

        ?>
        <div class="agl agl-enhancements agl-wishlist-embedded<?php echo esc_attr($theme . $wrapper_class); ?>" 
             data-slug="<?php echo esc_attr(isset($instance->args['menu_slug']) ? $instance->args['menu_slug'] : ''); ?>" 
             data-unique="<?php echo esc_attr($instance->unique); ?>">
            
            <div class="agl-container">
                <form method="post" action="<?php echo esc_attr($form_action); ?>" 
                      enctype="multipart/form-data" id="agl-form" autocomplete="off" novalidate="novalidate">
                    
                    <input type="hidden" class="agl-section-id" name="agl_transient[section]" value="1">
                    <?php wp_nonce_field('agl_enhancements_nonce', 'agl_enhancements_nonce' . $instance->unique); ?>

                    <!-- Form messages -->
                    <?php $this->renderFormMessages($instance); ?>

                    <div class="agl-wrapper<?php echo esc_attr($show_all); ?>">
                        <div class="agl-content">
                            <div class="agl-sections">
                                <?php foreach ($sections as $section): ?>
                                    <div class="agl-section agl-onload" data-section-id="<?php echo esc_attr(sanitize_title($section['title'] ?? '')); ?>">
                                        
                                        <?php if (!empty($section['title'])): ?>
                                            <div class="agl-section-title">
                                                <h3>
                                                    <?php if (!empty($section['icon'])): ?>
                                                        <i class="agl-section-icon <?php echo esc_attr($section['icon']); ?>"></i>
                                                    <?php endif; ?>
                                                    <?php echo esc_html($section['title']); ?>
                                                </h3>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($section['description'])): ?>
                                            <div class="agl-field agl-section-description">
                                                <?php echo wp_kses_post($section['description']); ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($section['fields'])): ?>
                                            <?php foreach ($section['fields'] as $field): ?>
                                                <?php $this->renderAGWISHGLUTField($field, $instance); ?>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="agl-no-option">
                                                <?php echo esc_html__('No enhancements available for this section.', 'wishglut'); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <?php if (!empty($instance->args['show_footer'])): ?>
                        <div class="agl-footer">
                            <div class="agl-buttons">
                                <input type="submit" name="agl_transient[save]" 
                                       class="button button-primary agl-save<?php echo esc_attr($ajax_class); ?>" 
                                       value="<?php echo esc_attr__('Save Settings', 'wishglut'); ?>" 
                                       data-save="<?php echo esc_attr__('Saving...', 'wishglut'); ?>">
                                
                                <?php if (isset($instance->args['show_reset_section']) && $instance->args['show_reset_section']): ?>
                                    <input type="submit" name="agl_transient[reset_section]" 
                                           class="button button-secondary agl-reset-section agl-confirm" 
                                           value="<?php echo esc_attr__('Reset Section', 'wishglut'); ?>" 
                                           data-confirm="<?php echo esc_attr__('Are you sure to reset this section enhancements?', 'wishglut'); ?>">
                                <?php endif; ?>
                            </div>
                            <div class="clear"></div>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        <?php
    }

    /**
     * Render individual AGWISHGLUT field
     */
    private function renderAGWISHGLUTField($field, $instance) {
        // Check for field errors
        if (method_exists($instance, 'error_check')) {
            $is_field_error = $instance->error_check($field);
            if (!empty($is_field_error)) {
                $field['_error'] = $is_field_error;
            }
        }

        // Set field default
        if (!empty($field['id']) && method_exists($instance, 'get_default')) {
            $field['default'] = $instance->get_default($field);
        }

        // Get field value
        $value = '';
        if (!empty($field['id']) && isset($instance->options[$field['id']])) {
            $value = $instance->options[$field['id']];
        }

        // Render using AGWISHGLUT
        AGWISHGLUT::field($field, $value, $instance->unique, 'options');
    }

    /**
     * Render form messages
     */
    private function renderFormMessages($instance) {
        if (isset($instance->args['show_form_warning']) && $instance->args['show_form_warning']) {
            echo '<div class="agl-form-result agl-form-warning">' . esc_html__('You have unsaved changes, save your changes!', 'wishglut') . '</div>';
        }

        $notice_class = (!empty($instance->notice)) ? 'agl-form-show' : '';
        $notice_text = (!empty($instance->notice)) ? $instance->notice : '';

        echo '<div class="agl-form-result agl-form-success ' . esc_attr($notice_class) . '">' . wp_kses_post($notice_text) . '</div>';
    }

    /**
     * Render basic enhancements form for fallback
     */
    private function renderBasicOptionsForm($instance) {
        ?>
        <div class="basic-enhancements-form">
            <h3><?php echo esc_html__('Settings', 'wishglut'); ?></h3>
            <form method="post" action="options.php">
                <?php settings_fields('agwishglut_wishlist_options'); ?>
                <table class="form-table">
                    <?php foreach ($instance->options as $key => $value): ?>
                        <tr>
                            <th scope="row">
                                <label for="<?php echo esc_attr($key); ?>">
                                    <?php echo esc_html(str_replace('-', ' ', ucwords($key, '-'))); ?>
                                </label>
                            </th>
                            <td>
                                <?php if (is_bool($value) || $value === '1' || $value === '0'): ?>
                                    <label>
                                        <input type="checkbox" 
                                               id="<?php echo esc_attr($key); ?>"
                                               name="agwishglut_wishlist_options[<?php echo esc_attr($key); ?>]" 
                                               value="1" 
                                               <?php checked($value, 1); ?>>
                                        <?php echo esc_html__('Enable', 'wishglut'); ?>
                                    </label>
                                <?php else: ?>
                                    <input type="text" 
                                           id="<?php echo esc_attr($key); ?>"
                                           name="agwishglut_wishlist_options[<?php echo esc_attr($key); ?>]" 
                                           value="<?php echo esc_attr($value); ?>" 
                                           class="regular-text">
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    /**
     * Get saved option value
     */
    public function getOption($option_key, $default = '') {
        $instance = $this->getInstanceWithFallback();
        
        if (!$instance || !isset($instance->options)) {
            // Fallback to direct database access
            $options = get_option('agwishglut_wishlist_options', array());
            return isset($options[$option_key]) ? $options[$option_key] : $default;
        }

        return isset($instance->options[$option_key]) ? $instance->options[$option_key] : $default;
    }

    /**
     * Get all options
     */
    public function getAllOptions() {
        $instance = $this->getInstanceWithFallback();
        
        if (!$instance || !isset($instance->options)) {
            return get_option('agwishglut_wishlist_options', array());
        }

        return $instance->options;
    }

    /**
     * Helper method to get tab URL with subtab support
     */
    private function getTabUrl($tab, $subtab = '') {
        $url = admin_url('admin.php?page=' . $this->menu_slug . '&tab=' . $tab);
        if ($subtab) {
            $url .= '&subtab=' . $subtab;
        }
        return $url;
    }

    private function renderSettingsStyles() {
        ?>
        <style>
        .wishglut-wishlist-settings .agl-enhancements {
             margin-top:0px;
			 padding-top:8px;
		}
        .basic-enhancements-form {
            background: #fff;
            padding: 20px;
            border: 1px solid #e1e5e9;
            border-radius: 6px;
        }
        
        .settings-nav-tabs {
            margin-bottom: 20px;
            border-bottom: 1px solid #ccc;
        }
        
        .settings-nav-tabs .nav-tab {
            font-size: 13px;
            padding: 8px 15px;
        }
        
        .agl-wishlist-embedded {
            border: 1px solid #e1e5e9;
            border-radius: 6px;
            background: #fff;
        }
        
        .agl-wishlist-embedded .agl-container {
            padding: 0;
        }
        
        .agl-wishlist-embedded .agl-header,
        .agl-wishlist-embedded .agl-nav {
            display: none !important;
        }
        
        .agl-wishlist-embedded .agl-wrapper {
            margin: 0;
            border: none;
            background: transparent;
        }
        
        .agl-wishlist-embedded .agl-content {
            padding: 20px;
            margin: 0;
        }
        </style>
        <?php
    }

    private function renderDashboardTab() {
        global $wpdb;
        
        // Get dashboard data
        $dashboard_data = $this->getDashboardData();
        
        ?>
        <div class="wishglut-dashboard-container">
            <div class="dashboard-header">
                <h1><?php echo esc_html__('Wishlist Dashboard', 'wishglut'); ?></h1>
                <p class="dashboard-subtitle"><?php echo esc_html__('Overview of your wishlist performance and analytics', 'wishglut'); ?></p>
            </div>

            <!-- Enhanced Key Metrics Cards with Guest Users -->
            <div class="dashboard-metrics-grid">
                <div class="metric-card">
                    <div class="metric-icon">🌐</div>
                    <div class="metric-content">
                        <h3><?php echo number_format($dashboard_data['total_users']); ?></h3>
                        <p><?php echo esc_html__('Total Users', 'wishglut'); ?></p>
                        <span class="metric-change <?php echo $dashboard_data['users_trend'] >= 0 ? 'positive' : 'negative'; ?>">
                            <?php echo $dashboard_data['users_trend'] >= 0 ? '+' : ''; ?><?php echo number_format($dashboard_data['users_trend']); ?>%
                        </span>
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-icon">🎡</div>
                    <div class="metric-content">
                        <h3><?php echo number_format($dashboard_data['guest_users']); ?></h3>
                        <p><?php echo esc_html__('Guest Users', 'wishglut'); ?></p>
                        <span class="metric-change <?php echo $dashboard_data['guest_trend'] >= 0 ? 'positive' : 'negative'; ?>">
                            <?php echo $dashboard_data['guest_trend'] >= 0 ? '+' : ''; ?><?php echo number_format($dashboard_data['guest_trend']); ?>%
                        </span>
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-icon">✅</div>
                    <div class="metric-content">
                        <h3><?php echo number_format($dashboard_data['registered_users']); ?></h3>
                        <p><?php echo esc_html__('Registered Users', 'wishglut'); ?></p>
                        <span class="metric-change <?php echo $dashboard_data['registered_trend'] >= 0 ? 'positive' : 'negative'; ?>">
                            <?php echo $dashboard_data['registered_trend'] >= 0 ? '+' : ''; ?><?php echo number_format($dashboard_data['registered_trend']); ?>%
                        </span>
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-icon">❤️</div>
                    <div class="metric-content">
                        <h3><?php echo number_format($dashboard_data['total_wishlists']); ?></h3>
                        <p><?php echo esc_html__('Total Wishlists', 'wishglut'); ?></p>
                        <span class="metric-change <?php echo $dashboard_data['wishlists_trend'] >= 0 ? 'positive' : 'negative'; ?>">
                            <?php echo $dashboard_data['wishlists_trend'] >= 0 ? '+' : ''; ?><?php echo number_format($dashboard_data['wishlists_trend']); ?>%
                        </span>
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-icon">📦</div>
                    <div class="metric-content">
                        <h3><?php echo number_format($dashboard_data['total_products']); ?></h3>
                        <p><?php echo esc_html__('Products in Wishlists', 'wishglut'); ?></p>
                        <span class="metric-change <?php echo $dashboard_data['products_trend'] >= 0 ? 'positive' : 'negative'; ?>">
                            <?php echo $dashboard_data['products_trend'] >= 0 ? '+' : ''; ?><?php echo number_format($dashboard_data['products_trend']); ?>%
                        </span>
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-icon">📊</div>
                    <div class="metric-content">
                        <h3><?php echo number_format($dashboard_data['avg_products_per_wishlist'], 1); ?></h3>
                        <p><?php echo esc_html__('Avg Products/Wishlist', 'wishglut'); ?></p>
                        <span class="metric-change <?php echo $dashboard_data['avg_trend'] >= 0 ? 'positive' : 'negative'; ?>">
                            <?php echo $dashboard_data['avg_trend'] >= 0 ? '+' : ''; ?><?php echo number_format($dashboard_data['avg_trend']); ?>%
                        </span>
                    </div>
                </div>
            </div>


            <!-- Recent Activity & Top Products -->
            <div class="dashboard-tables-section">
                <div class="table-container">
                    <div class="table-header">
                        <h3><?php echo esc_html__('Recent Wishlist Activity', 'wishglut'); ?></h3>
                        <div class="table-actions">
                            <select id="user-type-filter">
                                <option value="all"><?php echo esc_html__('All Users', 'wishglut'); ?></option>
                                <option value="registered"><?php echo esc_html__('Registered Users', 'wishglut'); ?></option>
                                <option value="guest"><?php echo esc_html__('Guest Users', 'wishglut'); ?></option>
                            </select>
                            <a href="#" class="view-all-btn">
                                <?php echo esc_html__('View All', 'wishglut'); ?>
                            </a>
                        </div>
                    </div>
                    <div class="table-content">
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th><?php echo esc_html__('User', 'wishglut'); ?></th>
                                    <th><?php echo esc_html__('Type', 'wishglut'); ?></th>
                                    <th><?php echo esc_html__('Product', 'wishglut'); ?></th>
                                    <th><?php echo esc_html__('Action', 'wishglut'); ?></th>
                                    <th><?php echo esc_html__('Date', 'wishglut'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dashboard_data['recent_activity'] as $activity): ?>
                                <tr data-user-type="<?php echo esc_attr($activity['user_type']); ?>">
                                    <td>
                                        <div class="user-info">
                                            <strong><?php echo esc_html($activity['username']); ?></strong>
                                            <span><?php echo esc_html($activity['useremail']); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="user-type-badge <?php echo esc_attr($activity['user_type']); ?>">
                                            <?php echo $activity['user_type'] === 'guest' ? esc_html__('Guest', 'wishglut') : esc_html__('Registered', 'wishglut'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="product-info">
                                            <strong><?php echo esc_html($activity['product_name']); ?></strong>
                                            <span><?php echo wp_kses_post( wc_price($activity['product_price']) ); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="action-badge <?php echo esc_attr($activity['action_type']); ?>">
                                            <?php echo esc_html($activity['action_text']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo esc_html($activity['date_formatted']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="table-container">
                    <div class="table-header">
                        <h3><?php echo esc_html__('Most Wishlisted Products', 'wishglut'); ?></h3>
                        <a href="#" class="view-all-btn export-btn" data-export="top-products">
                            <?php echo esc_html__('Export', 'wishglut'); ?>
                        </a>
                    </div>
                    <div class="table-content">
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th><?php echo esc_html__('Product', 'wishglut'); ?></th>
                                    <th><?php echo esc_html__('Times Wishlisted', 'wishglut'); ?></th>
                                    <th><?php echo esc_html__('By Guests', 'wishglut'); ?></th>
                                    <th><?php echo esc_html__('By Registered', 'wishglut'); ?></th>
                                    <th><?php echo esc_html__('Stock Status', 'wishglut'); ?></th>
                                    <th><?php echo esc_html__('Price', 'wishglut'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dashboard_data['top_products'] as $product_data): ?>
                                <tr>
                                    <td>
                                        <div class="product-info">
                                            <img src="<?php echo esc_url($product_data['image']); ?>" alt="<?php echo esc_attr($product_data['name']); ?>" class="product-thumb">
                                            <div>
                                                <strong><?php echo esc_html($product_data['name']); ?></strong>
                                                <span>ID: <?php echo esc_html($product_data['id']); ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="wishlist-count"><?php echo number_format($product_data['wishlist_count']); ?></span>
                                    </td>
                                    <td>
                                        <span class="guest-count"><?php echo number_format($product_data['guest_count']); ?></span>
                                    </td>
                                    <td>
                                        <span class="registered-count"><?php echo number_format($product_data['registered_count']); ?></span>
                                    </td>
                                    <td>
                                        <span class="stock-status <?php echo esc_attr($product_data['stock_status']); ?>">
                                            <?php echo esc_html($product_data['stock_text']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo wp_kses_post( wc_price($product_data['price']) ); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Enhanced Quick Actions with Export/Import -->
            <div class="dashboard-quick-actions">
                <div class="quick-actions-header">
                    <h3><?php echo esc_html__('Quick Actions', 'wishglut'); ?></h3>
                </div>
                <div class="quick-actions-grid">
                    <button class="quick-action-btn" data-action="export-all">
                        <span class="btn-icon">📤</span>
                        <span class="btn-text"><?php echo esc_html__('Export All Data', 'wishglut'); ?></span>
                    </button>
                    <button class="quick-action-btn" data-action="export-guest">
                        <span class="btn-icon">👤</span>
                        <span class="btn-text"><?php echo esc_html__('Export Guest Data', 'wishglut'); ?></span>
                    </button>
                    <button class="quick-action-btn" data-action="export-registered">
                        <span class="btn-icon">✅</span>
                        <span class="btn-text"><?php echo esc_html__('Export Registered Users', 'wishglut'); ?></span>
                    </button>
                    <button class="quick-action-btn" data-action="import-data" onclick="document.getElementById('import-file').click()">
                        <span class="btn-icon">📥</span>
                        <span class="btn-text"><?php echo esc_html__('Import Data', 'wishglut'); ?></span>
                    </button>
                    <input type="file" id="import-file" accept=".csv,.json" style="display: none;">
                    <button class="quick-action-btn" data-action="clear-old">
                        <span class="btn-icon">🗑️</span>
                        <span class="btn-text"><?php echo esc_html__('Clean Old Data', 'wishglut'); ?></span>
                    </button>
                  
                </div>
            </div>

            <!-- Import Modal -->
            <div id="import-modal" class="wishglut-modal" style="display: none;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3><?php echo esc_html__('Import Wishlist Data', 'wishglut'); ?></h3>
                        <span class="close-modal">&times;</span>
                    </div>
                    <div class="modal-body">
                        <div class="import-section">
                            <h4><?php echo esc_html__('Select Import Type', 'wishglut'); ?></h4>
                            <div class="import-enhancements">
                                <label>
                                    <input type="radio" name="import_type" value="merge" checked>
                                    <?php echo esc_html__('Merge with existing data', 'wishglut'); ?>
                                </label>
                                <label>
                                    <input type="radio" name="import_type" value="replace">
                                    <?php echo esc_html__('Replace existing data', 'wishglut'); ?>
                                </label>
                            </div>
                        </div>
                        <div class="import-section">
                            <h4><?php echo esc_html__('File Preview', 'wishglut'); ?></h4>
                            <div id="file-preview"></div>
                        </div>
                        <div class="import-section">
                            <h4><?php echo esc_html__('Field Mapping', 'wishglut'); ?></h4>
                            <div id="field-mapping"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                          <input type="file" id="import-file-input" accept=".csv,.json" style="display: none;">
                        <button class="button button-secondary" id="cancel-import"><?php echo esc_html__('Cancel', 'wishglut'); ?></button>
                        <button class="button button-primary" id="confirm-import"><?php echo esc_html__('Import', 'wishglut'); ?></button>
                    </div>
                </div>
            </div>
        </div>

        
        <?php
    }
   

   private function getDashboardData() {
    global $wpdb;
    
    $table_name = $this->table_shopg_wishlist();
    
    // Initialize data array
    $data = [];
    
    // Get current date for trends
    $current_date = current_time('Y-m-d');
    $last_month_date = gmdate('Y-m-d', strtotime('-30 days'));
    $last_week_date = gmdate('Y-m-d', strtotime('-7 days'));
    
    $total_user_sql=  "SELECT COUNT(DISTINCT wish_user_id) FROM " . $table_name;
    // Total Users (both registered and guest)
    $total_users = $wpdb->get_var( $total_user_sql); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared	
    
   $guest_user_sql = "SELECT COUNT(DISTINCT wish_user_id) FROM " . $table_name . " WHERE wish_user_id LIKE %s OR useremail LIKE %s OR username LIKE %s";

        $guest_users = $wpdb->get_var($wpdb->prepare($guest_user_sql, // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
            'guest_%',
            '%guest_%',
            'Guest%'
        ));
    
    // Registered Users
    $registered_users = $total_users - $guest_users;
    
    // Total Wishlists
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
    $total_wishlists = $wpdb->get_var(
        "SELECT COUNT(*) FROM {$table_name}" // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
    ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table statistics query
    
    // Total Products in Wishlists
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
    $total_products = $wpdb->get_var(
        "SELECT SUM(
            CASE
                WHEN product_ids = '' THEN 0
                ELSE (LENGTH(product_ids) - LENGTH(REPLACE(product_ids, ',', '')) + 1)
            END
        ) FROM {$table_name} WHERE product_ids != ''" // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
    );
    
    // Average products per wishlist
    $avg_products_per_wishlist = $total_wishlists > 0 ? ($total_products / $total_wishlists) : 0;
    
    // Get trends (last 30 days vs previous 30 days)
    $users_trend = $this->calculateTrend($table_name, 'wish_user_id', 'DISTINCT', 30);
    $guest_trend = $this->calculateGuestTrend($table_name, 30);
    $registered_trend = $this->calculateRegisteredTrend($table_name, 30);
    $wishlists_trend = $this->calculateTrend($table_name, 'id', 'COUNT', 30);
    $products_trend = $this->calculateProductsTrend($table_name, 30);
    $avg_trend = $this->calculateAverageTrend($table_name, 30);
    
    // Recent Activity (last 50 activities)
    $recent_activity_sql = "SELECT w.*, 
                CASE 
                    WHEN w.wish_user_id LIKE %s OR w.useremail LIKE %s OR w.username LIKE %s 
                    THEN 'guest' 
                    ELSE 'registered' 
                END as user_type,
                w.product_added_time as activity_date
         FROM {$table_name} w 
         ORDER BY w.product_added_time DESC 
         LIMIT 50";
    
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
    $recent_activity = $wpdb->get_results($wpdb->prepare(
// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        $recent_activity_sql,
        'guest_%',
        '%guest_%', 
        'Guest%'
    ));
    
    // Process recent activity
    $processed_activity = [];
    foreach ($recent_activity as $activity) {
        $product_ids = explode(',', $activity->product_ids);
        $product_dates = json_decode($activity->product_individual_dates, true);
        
        foreach ($product_ids as $index => $product_id) {
            if (empty($product_id)) continue;
            
            $product = wc_get_product($product_id);
            if (!$product) continue;
            
            $processed_activity[] = [
                'username' => $activity->username ?: 'Unknown User',
                'useremail' => $activity->useremail ?: 'No Email',
                'user_type' => $activity->user_type,
                'product_name' => $product->get_name(),
                'product_price' => $product->get_price(),
                'action_type' => 'added',
                'action_text' => __('Added to Wishlist', 'wishglut'),
                'date_formatted' => date('M j, Y g:i A', strtotime($activity->activity_date)) // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
            ];
            
            // Limit to 20 items for display
            if (count($processed_activity) >= 20) break 2;
        }
    }
    
    // Top Products
    $top_products_sql = "SELECT 
            SUBSTRING_INDEX(SUBSTRING_INDEX(w.product_ids, ',', numbers.n), ',', -1) as product_id,
            COUNT(*) as wishlist_count,
            SUM(CASE WHEN w.wish_user_id LIKE %s OR w.useremail LIKE %s OR w.username LIKE %s THEN 1 ELSE 0 END) as guest_count,
            SUM(CASE WHEN NOT (w.wish_user_id LIKE %s OR w.useremail LIKE %s OR w.username LIKE %s) THEN 1 ELSE 0 END) as registered_count
         FROM {$table_name} w
         JOIN (
            SELECT 1 n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5
            UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10
         ) numbers ON CHAR_LENGTH(w.product_ids) - CHAR_LENGTH(REPLACE(w.product_ids, ',', '')) >= numbers.n - 1
         WHERE w.product_ids != '' 
         AND SUBSTRING_INDEX(SUBSTRING_INDEX(w.product_ids, ',', numbers.n), ',', -1) != ''
         GROUP BY product_id
         ORDER BY wishlist_count DESC
         LIMIT 10";
    
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
    $top_products_query = $wpdb->get_results($wpdb->prepare(
// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        $top_products_sql,
        'guest_%', '%guest_%', 'Guest%',  // First set for guest_count
        'guest_%', '%guest_%', 'Guest%'   // Second set for registered_count
    ));
    
    $top_products = [];
    $top_products_labels = [];
    $top_products_data = [];
    
    foreach ($top_products_query as $product_data) {
        $product = wc_get_product($product_data->product_id);
        if (!$product) continue;
        
        $image_id = $product->get_image_id();
        $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'thumbnail') : wc_placeholder_img_src();
        
        $top_products[] = [
            'id' => $product_data->product_id,
            'name' => $product->get_name(),
            'image' => $image_url,
            'wishlist_count' => $product_data->wishlist_count,
            'guest_count' => $product_data->guest_count,
            'registered_count' => $product_data->registered_count,
            'stock_status' => $product->get_stock_status(),
            'stock_text' => $product->get_stock_status() === 'instock' ? __('In Stock', 'wishglut') : __('Out of Stock', 'wishglut'),
            'price' => $product->get_price()
        ];
        
        $top_products_labels[] = $product->get_name();
        $top_products_data[] = (int)$product_data->wishlist_count;
    }
    
    // Activity chart data (last 30 days)
    $activity_data = $this->getActivityChartData(30);
    
    // Compile all data
    $data = [
        'total_users' => (int)$total_users,
        'guest_users' => (int)$guest_users,
        'registered_users' => (int)$registered_users,
        'total_wishlists' => (int)$total_wishlists,
        'total_products' => (int)$total_products,
        'avg_products_per_wishlist' => (float)$avg_products_per_wishlist,
        'users_trend' => $users_trend,
        'guest_trend' => $guest_trend,
        'registered_trend' => $registered_trend,
        'wishlists_trend' => $wishlists_trend,
        'products_trend' => $products_trend,
        'avg_trend' => $avg_trend,
        'recent_activity' => $processed_activity,
        'top_products' => $top_products,
        'top_products_labels' => $top_products_labels,
        'top_products_data' => $top_products_data,
        'activity_labels' => $activity_data['labels'],
        'activity_data' => $activity_data['data'],
        'guest_activity_data' => $activity_data['guest_data']
    ];
    
    return $data;
   }

// Helper method to calculate trends
private function calculateTrend($table_name, $field, $function = 'COUNT', $days = 30) {
    global $wpdb;
    
    $current_period_start = date('Y-m-d', strtotime("-{$days} days")); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
    $previous_period_start = date('Y-m-d', strtotime("-" . ($days * 2) . " days")); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
    $previous_period_end = date('Y-m-d', strtotime("-{$days} days")); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
    
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
    $current_count = $wpdb->get_var($wpdb->prepare(
        "SELECT {$function}({$field}) FROM {$table_name} WHERE DATE(product_added_time) >= %s", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $current_period_start
    ));
    
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
    $previous_count = $wpdb->get_var($wpdb->prepare(
        "SELECT {$function}({$field}) FROM {$table_name} WHERE DATE(product_added_time) >= %s AND DATE(product_added_time) < %s", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $previous_period_start,
        $previous_period_end
    ));
    
    if ($previous_count == 0) {
        return $current_count > 0 ? 100 : 0;
    }
    
    return round((($current_count - $previous_count) / $previous_count) * 100, 1);
}

// Helper method to calculate guest user trend
private function calculateGuestTrend($table_name, $days = 30) {
    global $wpdb;
    
    $current_period_start = date('Y-m-d', strtotime("-{$days} days")); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
    $previous_period_start = date('Y-m-d', strtotime("-" . ($days * 2) . " days")); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
    $previous_period_end = date('Y-m-d', strtotime("-{$days} days")); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
    
    $guest_current_sql = "SELECT COUNT(DISTINCT wish_user_id) FROM {$table_name} 
         WHERE DATE(product_added_time) >= %s 
         AND (wish_user_id LIKE %s OR useremail LIKE %s OR username LIKE %s)";
    
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
    $current_count = $wpdb->get_var($wpdb->prepare(
// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        $guest_current_sql,
        $current_period_start,
        'guest_%',
        '%guest_%',
        'Guest%'
    ));
    
    $guest_previous_sql = "SELECT COUNT(DISTINCT wish_user_id) FROM {$table_name} 
         WHERE DATE(product_added_time) >= %s AND DATE(product_added_time) < %s
         AND (wish_user_id LIKE %s OR useremail LIKE %s OR username LIKE %s)";
    
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
    $previous_count = $wpdb->get_var($wpdb->prepare(
// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        $guest_previous_sql,
        $previous_period_start,
        $previous_period_end,
        'guest_%',
        '%guest_%',
        'Guest%'
    ));
    
    if ($previous_count == 0) {
        return $current_count > 0 ? 100 : 0;
    }
    
    return round((($current_count - $previous_count) / $previous_count) * 100, 1);
}

// Helper method to calculate registered user trend
private function calculateRegisteredTrend($table_name, $days = 30) {
    global $wpdb;
    
    $current_period_start = date('Y-m-d', strtotime("-{$days} days")); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
    $previous_period_start = date('Y-m-d', strtotime("-" . ($days * 2) . " days")); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
    $previous_period_end = date('Y-m-d', strtotime("-{$days} days")); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
    
    $registered_current_sql = "SELECT COUNT(DISTINCT wish_user_id) FROM {$table_name} 
         WHERE DATE(product_added_time) >= %s 
         AND NOT (wish_user_id LIKE %s OR useremail LIKE %s OR username LIKE %s)";
    
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
    $current_count = $wpdb->get_var($wpdb->prepare(
// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        $registered_current_sql,
        $current_period_start,
        'guest_%',
        '%guest_%',
        'Guest%'
    ));
    
    $registered_previous_sql = "SELECT COUNT(DISTINCT wish_user_id) FROM {$table_name} 
         WHERE DATE(product_added_time) >= %s AND DATE(product_added_time) < %s
         AND NOT (wish_user_id LIKE %s OR useremail LIKE %s OR username LIKE %s)";
    
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
    $previous_count = $wpdb->get_var($wpdb->prepare(
// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        $registered_previous_sql,
        $previous_period_start,
        $previous_period_end,
        'guest_%',
        '%guest_%',
        'Guest%'
    ));
    
    if ($previous_count == 0) {
        return $current_count > 0 ? 100 : 0;
    }
    
    return round((($current_count - $previous_count) / $previous_count) * 100, 1);
}

// Helper method to calculate products trend
private function calculateProductsTrend($table_name, $days = 30) {
    global $wpdb;
    
    $current_period_start = date('Y-m-d', strtotime("-{$days} days")); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
    $previous_period_start = date('Y-m-d', strtotime("-" . ($days * 2) . " days")); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
    $previous_period_end = date('Y-m-d', strtotime("-{$days} days")); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
    
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
    $current_count = $wpdb->get_var($wpdb->prepare(
        "SELECT SUM(
            CASE 
                WHEN product_ids = '' THEN 0 
                ELSE (LENGTH(product_ids) - LENGTH(REPLACE(product_ids, ',', '')) + 1) 
            END
        ) FROM {$table_name} WHERE DATE(product_added_time) >= %s AND product_ids != ''", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $current_period_start
    ));
    
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
    $previous_count = $wpdb->get_var($wpdb->prepare(
        "SELECT SUM(
            CASE 
                WHEN product_ids = '' THEN 0 
                ELSE (LENGTH(product_ids) - LENGTH(REPLACE(product_ids, ',', '')) + 1) 
            END
        ) FROM {$table_name} WHERE DATE(product_added_time) >= %s AND DATE(product_added_time) < %s AND product_ids != ''", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $previous_period_start,
        $previous_period_end
    ));
    
    if ($previous_count == 0) {
        return $current_count > 0 ? 100 : 0;
    }
    
    return round((($current_count - $previous_count) / $previous_count) * 100, 1);
}

// Helper method to calculate average trend
private function calculateAverageTrend($table_name, $days = 30) {
    global $wpdb;
    
    $current_period_start = date('Y-m-d', strtotime("-{$days} days")); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
    $previous_period_start = date('Y-m-d', strtotime("-" . ($days * 2) . " days")); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
    $previous_period_end = date('Y-m-d', strtotime("-{$days} days")); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
    
    // Current period
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
    $current_wishlists = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$table_name} WHERE DATE(product_added_time) >= %s", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $current_period_start
    ));
    
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
    $current_products = $wpdb->get_var($wpdb->prepare(
        "SELECT SUM(
            CASE 
                WHEN product_ids = '' THEN 0 
                ELSE (LENGTH(product_ids) - LENGTH(REPLACE(product_ids, ',', '')) + 1) 
            END
        ) FROM {$table_name} WHERE DATE(product_added_time) >= %s AND product_ids != ''", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $current_period_start
    ));
    
    $current_avg = $current_wishlists > 0 ? ($current_products / $current_wishlists) : 0;
    
    // Previous period
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
    $previous_wishlists = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$table_name} WHERE DATE(product_added_time) >= %s AND DATE(product_added_time) < %s", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $previous_period_start,
        $previous_period_end
    ));
    
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
    $previous_products = $wpdb->get_var($wpdb->prepare(
        "SELECT SUM(
            CASE 
                WHEN product_ids = '' THEN 0 
                ELSE (LENGTH(product_ids) - LENGTH(REPLACE(product_ids, ',', '')) + 1) 
            END
        ) FROM {$table_name} WHERE DATE(product_added_time) >= %s AND DATE(product_added_time) < %s AND product_ids != ''", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $previous_period_start,
        $previous_period_end
    ));
    
    $previous_avg = $previous_wishlists > 0 ? ($previous_products / $previous_wishlists) : 0;
    
    if ($previous_avg == 0) {
        return $current_avg > 0 ? 100 : 0;
    }
    
    return round((($current_avg - $previous_avg) / $previous_avg) * 100, 1);
}

// Helper method to get activity chart data
private function getActivityChartData($days = 30) {
    global $wpdb;
    
    $table_name = $this->table_shopg_wishlist();
    $start_date = date('Y-m-d', strtotime("-{$days} days")); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
    
    $labels = [];
    $data = [];
    $guest_data = [];
    
    for ($i = $days - 1; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-{$i} days")); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
        $labels[] = date('M j', strtotime($date)); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
        
        // Total wishlists for this date
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $total_count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table_name} WHERE DATE(product_added_time) = %s", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            $date
        ));
        
        // Guest wishlists for this date
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $guest_count = $wpdb->get_var($wpdb->prepare(
// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            "SELECT COUNT(*) FROM {$table_name} 
             WHERE DATE(product_added_time) = %s 
             AND (wish_user_id LIKE 'guest_%' OR useremail LIKE '%guest_%' OR username LIKE 'Guest%')", // phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.LikeWildcardsInQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            $date
        ));
        
        $data[] = (int)$total_count;
        $guest_data[] = (int)$guest_count;
    }
    
    return [
        'labels' => $labels,
        'data' => $data,
        'guest_data' => $guest_data
    ];
}

// Helper method to get table name
private function table_shopg_wishlist() {
    global $wpdb;
    return $wpdb->prefix . 'wishglut_wishlist';
}


private function getRecentActivity($limit = 10) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wishglut_wishlist';
    
    $activities_sql = "SELECT * FROM {$table_name} ORDER BY product_added_time DESC LIMIT %d";
    
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
    $activities = $wpdb->get_results($wpdb->prepare(
// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        $activities_sql,
        $limit
    ));
    
    $result = [];
    foreach ($activities as $activity) {
        $product_ids = array_filter(explode(',', $activity->product_ids));
        $first_product_id = !empty($product_ids) ? $product_ids[0] : 0;
        $product = wc_get_product($first_product_id);
        
        if ($product) {
            $result[] = [
                'username' => $activity->username ?: 'Guest User',
                'useremail' => $activity->useremail ?: 'guest@example.com',
                'product_name' => $product->get_name(),
                'product_price' => $product->get_price(),
                'action_type' => 'added',
                'action_text' => __('Added to Wishlist', 'wishglut'),
                'date_formatted' => date('M j, Y g:i A', strtotime($activity->product_added_time)) // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
            ];
        }
    }
    
    return $result;
}


private function getTopProducts($limit = 10) {
    global $wpdb;
    
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
    $table_name = $wpdb->prefix . 'wishglut_wishlist';
    
    // Get all product IDs from wishlists
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
    $product_ids_query = $wpdb->get_col("SELECT product_ids FROM {$table_name} WHERE product_ids != ''"); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching	
    
    $product_counts = [];
    foreach ($product_ids_query as $product_ids) {
        $ids = array_filter(explode(',', $product_ids));
        foreach ($ids as $id) {
            $id = trim($id);
            if (!empty($id)) {
                $product_counts[$id] = isset($product_counts[$id]) ? $product_counts[$id] + 1 : 1;
            }
        }
    }
    
    // Sort by count
    arsort($product_counts);
    
    // Get top products
    $top_products = [];
    $count = 0;
    foreach ($product_counts as $product_id => $wishlist_count) {
        if ($count >= $limit) break;
        
        $product = wc_get_product($product_id);
        if ($product) {
            $top_products[] = [
                'id' => $product_id,
                'name' => $product->get_name(),
                'wishlist_count' => $wishlist_count,
                'price' => $product->get_price(),
                'image' => wp_get_attachment_image_url($product->get_image_id(), 'thumbnail'),
                'stock_status' => $product->is_in_stock() ? 'in_stock' : 'out_of_stock',
                'stock_text' => $product->is_in_stock() ? __('In Stock', 'wishglut') : __('Out of Stock', 'wishglut')
            ];
            $count++;
        }
    }
    
    return $top_products;
}


private function getTotalWishlists() {
    return 1247;
}

private function renderStyles() {
    // Your existing styles
}

public function enqueueAssets($hook) {
    // Only load on wishglut_wishlist pages (Wishglut submenu) or wishglut (individual menu)
    if (strpos($hook, 'wishglut_wishlist') === false && strpos($hook, 'wishglut') === false) {
        return;
    }

    $plugin_url = plugin_dir_url(dirname(__FILE__));

    // Enqueue Admin CSS
    if (file_exists(dirname(__FILE__) . '/assets/wishlist-admin.css')) {
        wp_enqueue_style(
            'wishglut-wishlist-admin',
            $plugin_url . 'src/assets/wishlist-admin.css',
            [],
            filemtime(dirname(__FILE__) . '/assets/wishlist-admin.css')
        );
    }

    // Enqueue Admin JS
    if (file_exists(dirname(__FILE__) . '/assets/wishlist-admin.js')) {
        wp_enqueue_script(
            'wishglut-wishlist-admin-js',
            $plugin_url . 'src/assets/wishlist-admin.js',
            ['jquery'],
            filemtime(dirname(__FILE__) . '/assets/wishlist-admin.js'),
            true
        );

        // Localize script
        wp_localize_script('wishglut-wishlist-admin-js', 'wishglut_wishlist_admin_dashboard', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wishglut_wishlist_nonce')
        ));
    }

    // Enqueue jQuery UI for sortable/draggable if needed
    wp_enqueue_script('jquery-ui-sortable');
    wp_enqueue_script('jquery-ui-draggable');
    wp_enqueue_script('jquery-ui-droppable');
}

}