<?php

use Techbizz\UnitConverterModule\Controllers\MainController;

final readonly class PluginAdapter
{
    const CONVERTER_FORM_NONCE = 'converter-form-nonce';

    public function __construct(protected MainController $serviceController)
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
        add_shortcode('converter_form', array($this, 'displayForm'));

        add_action('wp_ajax_handleForm', [$this, 'handleForm']);
        add_action('wp_ajax_nopriv_handleForm', [$this, 'handleForm']);
    }

    public function enqueueScripts(): void
    {
        wp_enqueue_script('ajax-form-script', plugin_dir_url(__FILE__) . 'resources/script.js', array('jquery'), '1.0', true);
        wp_localize_script('ajax-form-script', 'ajax_object', ['ajax_url' => admin_url('admin-ajax.php')]);
    }

    public function displayForm(): string
    {
        $nonce = wp_create_nonce( self::CONVERTER_FORM_NONCE );
        $action = admin_url("admin-ajax.php?_wpnonce=$nonce");
        $content = '<div>';
        $content .= sprintf('<form id="ajax-form" action="#" data-url="%s" method="post">', $action);
        $content .= '<input type="hidden" name="action" value="handleForm">';
        $content .= '<input type="text" name="f" value="aana" placeholder="aana"/>';
        $content .= '<input type="text" name="t" value="ropani" placeholder="ropani"/>';
        $content .= '<input type="text" name="v" value="11" placeholder="value"/>';
        $content .= '<button type="submit">Submit</button>';
        $content .= '</form>';
        $content .= '<div id="error" style="color:red"></div>';
        $content .= '</div>';
        return $content;
    }

    /**
     * @throws Exception
     */
    public function handleForm(): void
    {
        if (!DOING_AJAX) {
            $return = ["code" => 400, "message" => 'no json, no work !!!'];
            wp_send_json_error($return, $return['code']);
            wp_die();
        }
        if((! wp_verify_nonce($_GET['_wpnonce'], self::CONVERTER_FORM_NONCE))){
            wp_send_json_error('Security check failed', 500);
            wp_die();
        }

        $args = array_filter($_POST, fn($key) => in_array($key, ['f','t','v']), ARRAY_FILTER_USE_KEY);

        try {
            $response = ['code' => 200, 'result' => $this->serviceController->convert($args)];
            wp_send_json_success($response);
        } catch (Exception $exception) {
            $response = ["code" => 400, "message" => $exception->getMessage()];
            wp_send_json_error($response, $response['code']);
        }
        wp_die();
    }
}