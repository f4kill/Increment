<?php

/*
Plugin Name: Increment
Description: Add a shortcode to generate a button which increments a value and shows the value once pressed
Version: 1.0
Author: Aymeric Bianco Pelle
Author URI: http://aymericbiancopelle.com
License: GPL2
*/

namespace Increment;

use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

const PREFIX         = 'increment-';
const REST_NAMESPACE = 'increment/v1';
const REST_ENDPOINT  = 'id';

/**
 * Plugin's shortcode callback
 * Generates a button and response element. The button increments a value corresponding to the id.
 *
 * @param $atts
 * @param $content
 * @param $shortcode_tag
 *
 * @return string
 */
function shortcode($atts, $content, $shortcode_tag) {
    if(!isset($atts['id']) || empty($atts['id'])) {
        return sprintf('<p class="increment-error">%s</p>', __('Merci de spécifier un identifiant pour sauvegarder la valeur', 'increment'));
    }

    $defaults = array(
        'id' => '',
        'button_class' => '',
        'show_immediately' => false,
        'wrap_tag' => 'span',
        'wrap_class' => '',
        'before' => '',
        'after' => '',
    );

    $options = array_merge($defaults, $atts);
    extract($options);

    $id = esc_attr($id);

    $button_class = esc_attr($button_class);

    $show_immediately = boolval($show_immediately);
    $wrap_tag = preg_replace('/[^A-Za-z]/', '', $wrap_tag);
    $wrap_class = esc_attr($wrap_class);

    $value = get_option(PREFIX . $id, 0);
    $value_style = $show_immediately ? '' : 'display:none;';

    $option_name = PREFIX . $id;

    $html = "<button class='increment-button $option_name $button_class' data-id='$id'>$content</button>\n";
    $html .= "<$wrap_tag class='increment-response $wrap_class' style='$value_style'>";
    $html .= "$before<span class='increment-response-value'>$value</span>$after";
    $html .= "</$wrap_tag>\n";

    return $html;
}

/**
 * Register plugin's shortcode
 */
function register_shortcode() {
    add_shortcode('increment-button', 'Increment\shortcode');
}

add_action('init', 'Increment\register_shortcode');

/**
 * Register plugin scripts
 */
function register_script() {
    wp_register_script('increment-js', plugin_dir_url( __FILE__ ) . 'public/js/increment.js');
    wp_enqueue_script('increment-js');

    $rest_path = REST_NAMESPACE . '/' . REST_ENDPOINT;
    $rest_url = rest_url($rest_path);

    wp_localize_script( 'increment-js', 'incrementLoc', array(
        'url' => esc_url_raw($rest_url),
    ) );
}
add_action( 'wp_enqueue_scripts', 'Increment\register_script' );

/**
 * Increment option by 1
 *
 * @param WP_REST_Request $request
 * @return WP_Error|WP_REST_Response New incremented value
 */
function REST_add(WP_REST_Request $request) {
    $params = $request->get_params();

    if (!array_key_exists('id', $params)) {
        return new WP_Error('missing_parameter', 'Please provide an id', $params);
    }

    $value = get_option(PREFIX . $params['id']);
    $new_value = $value + 1;
    $update_ok = update_option(PREFIX . $params['id'], $new_value);

    if (!$update_ok) {
        return new WP_Error('update_error', 'Something went wrong when updating value', $params);
    }

    $response = array(
        'code' => 'ok',
        'data' => array(
            'value' => $new_value,
        ),
    );

    return new WP_REST_Response($response, 200);
}

/**
 * Get option value
 *
 * @param WP_REST_Request $request
 * @return WP_Error|WP_REST_Response The value
 */
function REST_get(WP_REST_Request $request) {
    $params = $request->get_params();

    if(!array_key_exists('id', $params)) {
        return new WP_Error('missing_parameter', 'Please provide an id', $params);
    }

    $value = get_option(PREFIX . $params['id']);

    return new WP_REST_Response($value);
}

/**
 * Register REST endpoints
 */
function REST_init () {
    register_rest_route( REST_NAMESPACE, REST_ENDPOINT, array(
        array(
            'methods' => \WP_REST_Server::EDITABLE,
            'callback' => 'Increment\REST_add',
        ),
        array(
            'methods' => \WP_REST_Server::READABLE,
            'callback' => 'Increment\REST_get'
        )
    ) );
}
add_action( 'rest_api_init', 'Increment\REST_init' );








add_action( 'admin_menu', 'Increment\add_plugin_page' );
//add_action( 'admin_init', array( $this, 'page_init' ) );

function add_plugin_page() {
    add_options_page(
        'Increment',
        __('Incrément', 'increment'),
        'edit_posts',
        'increment-documentation',
        'Increment\create_documentation_page',
    );
}


function create_documentation_page() {
    // TODO generate with loop
    // TODO put options and default value in a constant or a property
    ?>
    <div class="wrap">
        <h1><?= __( 'Fonctionnement', 'increment' ); ?> </h1>
        <p>Le code court <code>[increment-button]</code> nécessite au minimum deux arguments :</p>
        <table class="wp-list-table widefat striped">
            <thead>
                <tr>
                    <th class="manage-column">Paramètre</th>
                    <th class="manage-column">Type attendu</th>
                    <th class="manage-column">Valeur par défaut</th>
                    <th class="manage-column">Description</th>
                    <th class="manage-column">Exemple</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>contenu</td>
                    <td>chaine de caractères</td>
                    <td>aucune</td>
                    <td>Libellé du bouton</td>
                    <td><code>[increment-button id="pommes"]Ajouter[/increment-button]</code></td>
                </tr>
                <tr>
                    <td>id</td>
                    <td>chaine de caractères</td>
                    <td>aucune</td>
                    <td>Permet d'identifier la valeur devant être incrémentée.</td>
                    <td><code>id="visiteurs"</code></td>
                </tr>
            </tbody>
        </table>

        <p>Les paramêtres optionnels suivants sont également pris en compte :</p>
        <table class="wp-list-table widefat striped">
            <thead>
                <tr>
                    <th class="manage-column">Paramètre</th>
                    <th class="manage-column">Type attendu</th>
                    <th class="manage-column">Valeur par défaut</th>
                    <th class="manage-column">Description</th>
                    <th class="manage-column">Exemple</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>button_class</code></td>
                    <td><i>chaine de caractères</i></td>
                    <td>vide</td>
                    <td>Classe css à ajouter au bouton. </td>
                    <td><code>button_class="bouton-visiteurs"</code></td>
                </tr>
                <tr>
                    <td><code>show_immediately</code></td>
                    <td><code>0</code> <i>ou</i> <code>1</code></td>
                    <td><code>0</code></td>
                    <td>Afficher ou non la valeur dès le chargement de la page. Si <code>0</code> la valeur sera affichée après appui sur le bouton d'incrément. </td>
                    <td><code>value_show="1"</code></td>
                </tr>
                <tr>
                    <td><code>wrap_tag</code></td>
                    <td><i>chaine de caractères</i></td>
                    <td><code>span</code></td>
                    <td>Nom du tag qui entoure la valeur. </td>
                    <td><code>wrap_tag="p"</code></td>
                </tr>
                <tr>
                    <td><code>wrap_class</code></td>
                    <td><i>chaine de caractères</i></td>
                    <td>vide</td>
                    <td>Classe css à ajouter au tag entourant la valeur. </td>
                    <td><code>wrap_class="ma-classe"</code></td>
                </tr>
                <tr>
                    <td><code>before</code></td>
                    <td><i>chaine de caractères</i></td>
                    <td>vide</td>
                    <td>Contenu à ajouter avant la valeur. </td>
                    <td><code>before="Il y a eu "</code></td>
                </tr>
                <tr>
                    <td><code>after</code></td>
                    <td><i>chaine de caractères</i></td>
                    <td>vide</td>
                    <td>Contenu à ajouter après la valeur. </td>
                    <td><code>after=" visiteurs &lt;i&gt;aujourd'hui&lt;/i&gt;"</code></td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php
}