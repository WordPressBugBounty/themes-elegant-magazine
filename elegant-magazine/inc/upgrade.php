<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;


// Exit if accessed directly.
defined('ABSPATH') || exit;

class ElegantMagazine_Notice
{
    public $name;
    public $type;
    public $dismiss_url;
    public $temporary_dismiss_url;
    public $pricing_url;
    public $current_user_id;

    /**
     * The constructor.
     *
     * @param string $name Notice Name.
     * @param string $type Notice type.
     * @param string $dismiss_url Notice permanent dismiss URL.
     * @param string $temporary_dismiss_url Notice temporary dismiss URL.
     *
     * @since 1.4.7
     *
     */
    public function __construct($name, $type, $dismiss_url, $temporary_dismiss_url)
    {
        $this->name = $name;
        $this->type = $type;
        $this->dismiss_url = $dismiss_url;
        $this->temporary_dismiss_url = $temporary_dismiss_url;
        $this->pricing_url = 'https://afthemes.com/products/elegant-magazine-pro/';
        $this->current_user_id = get_current_user_id();

        // Notice markup.
        add_action('admin_notices', array($this, 'notice'));

        $this->dismiss_notice();
        $this->dismiss_notice_temporary();
    }

    public function notice()
    {
        if (!$this->is_dismiss_notice()) {
            $this->notice_markup();
        }
    }

    private function is_dismiss_notice()
    {
        return apply_filters('elegant_magazine_' . $this->name . '_notice_dismiss', true);
    }

    public function notice_markup()
    {
        echo '';
    }

    /**
     * Hide a notice if the GET variable is set.
     */
    public function dismiss_notice()
    {
        if (isset($_GET['elegant_magazine_upgrade_notice_dismiss']) && isset($_GET['_elegant_magazine_upgrade_notice_dismiss_nonce'])) { // WPCS: input var ok.
            if (!wp_verify_nonce(wp_unslash($_GET['_elegant_magazine_upgrade_notice_dismiss_nonce']), 'elegant_magazine_upgrade_notice_dismiss_nonce')) { // phpcs:ignore WordPress.VIP.ValidatedSanitizedInput.InputNotSanitized
                wp_die(__('Action failed. Please refresh the page and retry.', 'elegant-magazine')); // WPCS: xss ok.
            }

            if (!current_user_can('publish_posts')) {
                wp_die(__('Cheatin&#8217; huh?', 'elegant-magazine')); // WPCS: xss ok.
            }

            $dismiss_notice = sanitize_text_field(wp_unslash($_GET['elegant_magazine_upgrade_notice_dismiss']));

            // Hide.
            if ($dismiss_notice === $_GET['elegant_magazine_upgrade_notice_dismiss']) {
                add_user_meta(get_current_user_id(), 'elegant_magazine_' . $dismiss_notice . '_notice_dismiss', 'yes', true);
            }
        }
    }

    public function dismiss_notice_temporary()
    {
        if (isset($_GET['elegant_magazine_upgrade_notice_dismiss_temporary']) && isset($_GET['_elegant_magazine_upgrade_notice_dismiss_temporary_nonce'])) { // WPCS: input var ok.
            if (!wp_verify_nonce(wp_unslash($_GET['_elegant_magazine_upgrade_notice_dismiss_temporary_nonce']), 'elegant_magazine_upgrade_notice_dismiss_temporary_nonce')) { // phpcs:ignore WordPress.VIP.ValidatedSanitizedInput.InputNotSanitized
                wp_die(__('Action failed. Please refresh the page and retry.', 'elegant-magazine')); // WPCS: xss ok.
            }

            if (!current_user_can('publish_posts')) {
                wp_die(__('Cheatin&#8217; huh?', 'elegant-magazine')); // WPCS: xss ok.
            }

            $dismiss_notice = sanitize_text_field(wp_unslash($_GET['elegant_magazine_upgrade_notice_dismiss_temporary']));

            // Hide.
            if ($dismiss_notice === $_GET['elegant_magazine_upgrade_notice_dismiss_temporary']) {
                add_user_meta(get_current_user_id(), 'elegant_magazine_' . $dismiss_notice . '_notice_dismiss_temporary', 'yes', true);
            }
        }
    }
}


class ElegantMagazine_Upgrade_Notice extends ElegantMagazine_Notice {

    public function __construct() {
        if ( ! current_user_can( 'publish_posts' ) ) {
            return;
        }

        $dismiss_url = wp_nonce_url(
            add_query_arg( 'elegant_magazine_upgrade_notice_dismiss', 'upgrade', admin_url() ),
            'elegant_magazine_upgrade_notice_dismiss_nonce',
            '_elegant_magazine_upgrade_notice_dismiss_nonce'
        );

        $temporary_dismiss_url = wp_nonce_url(
            add_query_arg( 'elegant_magazine_upgrade_notice_dismiss_temporary', 'upgrade', admin_url() ),
            'elegant_magazine_upgrade_notice_dismiss_temporary_nonce',
            '_elegant_magazine_upgrade_notice_dismiss_temporary_nonce'
        );

        parent::__construct( 'upgrade', 'info', $dismiss_url, $temporary_dismiss_url );

        $this->set_notice_time();

        $this->set_temporary_dismiss_notice_time();

        $this->set_dismiss_notice();
    }

    private function set_notice_time() {
        if ( ! get_option( 'elegant_magazine_upgrade_notice_start_time' ) ) {
            update_option( 'elegant_magazine_upgrade_notice_start_time', time() );
        }
    }

    private function set_temporary_dismiss_notice_time() {
        if ( isset( $_GET['elegant_magazine_upgrade_notice_dismiss_temporary'] ) && 'upgrade' === $_GET['elegant_magazine_upgrade_notice_dismiss_temporary'] ) {
            update_user_meta( $this->current_user_id, 'elegant_magazine_upgrade_notice_dismiss_temporary_start_time', time() );
        }
    }

    public function set_dismiss_notice() {

        /**
         * Do not show notice if:
         *
         * 1. It has not been 5 days since the theme is activated.
         * 2. If the user has ignored the message partially for 2 days.
         * 3. Dismiss always if clicked on 'Dismiss' button.
         */
        if ( get_option( 'elegant_magazine_upgrade_notice_start_time' ) > strtotime( '-2 minutes' )
            || get_user_meta( get_current_user_id(), 'elegant_magazine_upgrade_notice_dismiss', true )
            || get_user_meta( get_current_user_id(), 'elegant_magazine_upgrade_notice_dismiss_temporary_start_time', true ) > strtotime( '-2 day' )
        ) {
            add_filter( 'elegant_magazine_upgrade_notice_dismiss', '__return_true' );
        } else {
            add_filter( 'elegant_magazine_upgrade_notice_dismiss', '__return_false' );
        }
    }

    public function notice_markup() {
        ?>
        <div class="notice notice-success elegant-magazine-notice" >
            <a class="elegant-magazine-notice-dismiss notice-dismiss" href="<?php echo esc_url( $this->dismiss_url ); ?>"></a>

            <p class="notice-text">
                <?php
                $current_user = wp_get_current_user();

                printf(
                /* Translators: %1$s current user display name., %2$s this theme name., %3$s discount coupon code., %4$s discount percentage. */
                   esc_html__(
                        '%1$s 🎁 Hope you\'re loving our free %2$s theme! Dive into the festive spirit with an exclusive gift: %4$s premium features! Use code %3$s at checkout. Wishing you a season filled with joyous holidays and even happier savings! 🎄✨',
                        'elegant-magazine'
                    ),
                    '<h3> Greetings ' . esc_html( $current_user->display_name ) . '! Ready for an Upgrade? Unlock a Special Treat: 30% Off!</h3>',
                    '<a href="https://afthemes.com/products/elegant-magazine/">Elegant Magazine</a>',
                    '<code class="coupon-code">AFT30</code>',
                    '<strong>30% off</strong>'
                );
                ?>
            </p>

            <div class="links">
                <a href="<?php echo esc_url( $this->pricing_url ); ?>" class="button button-primary" target="_blank">

                    <span><?php esc_html_e( 'Claim Your Discount', 'elegant-magazine' ); ?></span>
                </a>

                <a href="<?php echo esc_url( $this->pricing_url ); ?>" class="button button-secondary" target="_blank">

                    <span><?php esc_html_e( 'Power Bundle', 'elegant-magazine' ); ?></span>
                </a>

                <a href="https://afthemes.com/all-themes-plan/" class="button button-secondary" target="_blank">

                    <span><?php esc_html_e( 'All Themes Plan', 'elegant-magazine' ); ?></span>
                </a>

                <a href="<?php echo esc_url( $this->temporary_dismiss_url ); ?>" class="button button-normal">

                    <span><?php esc_html_e( 'Maybe Later', 'elegant-magazine' ); ?></span>
                </a>

                <a href="https://afthemes.com/supports/" class="button button-normal" target="_blank">

                    <span><?php esc_html_e( 'Have Queries?', 'elegant-magazine' ); ?></span>
                </a>
            </div>
        </div> <!-- /elegant-magazine-notice -->
        <?php
    }
}

new ElegantMagazine_Upgrade_Notice();
