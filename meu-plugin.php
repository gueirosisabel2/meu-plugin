/**
 * Plugin Name: Meu Plugin CRUD
 * Plugin URI: https://www.isabeldesign.com.br/
 * Description: Plugin CRUD para WordPress com consulta no banco de dados birdpa30_migw e nas tabelas redirect_limit e redirect_ZDG.
 * Version: 1.0
 * Author: Seu Nome
 * Author URI: https://www.exemplo.com/
 * License: GPL2
 */


<?php
// Inclua o arquivo do plugin
include_once( plugin_dir_path( __FILE__ ) . 'meu-plugin.php' );

// Verifique se um novo registro foi enviado pelo formulário
adicionar_registro();

// Crie a página personalizada de administração
function meu_plugin_admin_page() {
    ?>
    <div class="wrap">
        <h1>Meu Plugin CRUD</h1>
        <?php listar_registros(); ?>
        <hr>
        <h2>Adicionar Registro</h2>
        <form method="post">
            <label for="redirect_url">URL para Redirect:</label>
            <input type="text" id="redirect_url" name="redirect_url" required>
            <br><br>
            <label for="limit_count">Número de Cliques</label>
            <input type="number" id="limit_count" name="limit_count" required>
            <br><br>
            <input type="submit" name="adicionar_registro" class="button button-primary" value="Adicionar Registro">
        </form>
    </div>
    <?php
}
// Adicione a página personalizada de administração
add_action( 'admin_menu', 'adicionar_pagina_administracao' );
function adicionar_pagina_administracao() {
    add_menu_page(
        'Meu Plugin CRUD',
        'Meu Plugin CRUD',
        'manage_options',
        'meu-plugin-admin',
        'meu_plugin_admin_page',
        'dashicons-list-view',
        20
    );
}

// Incluindo o arquivo de conexão com o banco de dados
include_once( ABSPATH . 'wp-config.php' );

// Função para criar as tabelas do plugin
function criar_tabelas() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $table_name1 = $wpdb->prefix . 'redirect_limit';
    $table_name2 = $wpdb->prefix . 'redirect_ZDG';

    $sql1 = "CREATE TABLE $table_name1 (
        id INT(11) NOT NULL AUTO_INCREMENT,
        redirect_id INT(11) NOT NULL,
        limit_count INT(11) NOT NULL,
        PRIMARY KEY (id),
        FOREIGN KEY (redirect_id) REFERENCES $table_name2(id)
    ) $charset_collate;";

    $sql2 = "CREATE TABLE $table_name2 (
        id INT(11) NOT NULL AUTO_INCREMENT,
        redirect_url VARCHAR(255) NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql1 );
    dbDelta( $sql2 );
}
register_activation_hook( __FILE__, 'criar_tabelas' );

// Função para adicionar um novo registro
function adicionar_registro() {
    global $wpdb;
    $table_name1 = $wpdb->prefix . 'redirect_limit';
    $table_name2 = $wpdb->prefix . 'redirect_ZDG';

    if ( isset( $_POST['adicionar_registro'] ) ) {
        $redirect_url = $_POST['redirect_url'];
        $limit_count = $_POST['limit_count'];

        // Inserindo o registro na tabela redirect_ZDG
        $wpdb->insert( $table_name2, array( 'redirect_url' => $redirect_url ) );

        // Pegando o ID do registro inserido na tabela redirect_ZDG
        $redirect_id = $wpdb->insert_id;

        // Inserindo o registro na tabela redirect_limit
        $wpdb->insert( $table_name1, array( 'redirect_id' => $redirect_id, 'limit_count' => $limit_count ) );

        echo '<div class="notice notice-success"><p>Registro adicionado com sucesso!</p></div>';
    }
}
function meu_plugin_admin_page() {
    // Verifica se uma ação foi enviada
    if ( isset( $_GET['action'] ) ) {
        // Verifica se a ação é editar
        if ( $_GET['action'] == 'editar' ) {
            editar_registro();
        }
        // Verifica se a ação é excluir
        elseif ( $_GET['action'] == 'excluir' ) {
            excluir_registro();
        }
    }
    // Se não houver ação, exibe a listagem de registros e o formulário de adição
    else {
        ?>
        <div class="wrap">
            <h1>Meu Plugin CRUD</h1>
            <?php listar_registros(); ?>
            <hr>
            <h2>Adicionar Registro</h2>
            <<form method="post">
                <label for="redirect_url">URL para Redirect:</label>
                <input type="text" id="redirect_url" name="redirect_url" required>
                <br><br>
                <label for="limit_count">Número de Cliques</label>
                <input type="number" id="limit_count" name="limit_count" required>
                <br><br>
                <input type="submit" name="adicionar_registro" class="button button-primary" value="Adicionar Registro">
            </form>
        </div>
        <?php
    }
}

// Função para editar um registro
function editar_registro() {
    global $wpdb;
    $table_name1 = $wpdb->prefix . 'redirect_limit';
    $table_name2 = $wpdb->prefix . 'redirect_ZDG';

    if ( isset( $_POST['editar_registro'] ) ) {
        $id = $_POST['id'];
        $redirect_url = $_POST['redirect_url'];
        $limit_count = $_POST['limit_count'];

        // Atualizando o registro na tabela redirect_ZDG
        $wpdb->update( $table_name2, array( 'redirect_url' => $redirect_url ), array( 'id' => $id ) );

        // Atualizando o registro na tabela redirect_limit
        $wpdb->update( $table_name1, array( 'limit_count' => $limit_count ), array( 'redirect_id' => $id ) );

        echo '<div class="notice notice-success"><p>Registro atualizado com sucesso!</p></div>';
    } else {
        $id = $_GET['id'];

        // Buscando o registro na tabela redirect_ZDG
        $registro_zdg = $wpdb->get_row( "SELECT * FROM $table_name2 WHERE id = $id" );

        // Buscando o registro na tabela redirect_limit
        $registro_limit = $wpdb->get

// Função para listar os registros
function listar_registros() {
    global $wpdb;
    $table_name1 = $wpdb->prefix . 'redirect_limit';
    $table_name2 = $wpdb->prefix . 'redirect_ZDG';

    $registros = $wpdb->get_results( "SELECT $table_name1.id, $table_name2.redirect_url, $table_name1.limit_count FROM $table_name1 INNER JOIN $table_name2 ON $table_name1.redirect_id = $table_name2.id ORDER BY $table_name1.id DESC" );

    if ( $registros ) {
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr><th>ID</th><th>Redirect URL</th><th>Limit Count</th></tr></thead>';
        echo '<tbody>';

        foreach ( $registros as $registro ) {
            echo '<tr>';
            echo '<td>' . $registro->id . '</td>';
            echo '<td>' . $registro->redirect_url . '</td>';
            echo '<td>' . $registro->limit_count . '</td>';
            echo '<td><a href="?page=meu-plugin-admin&action=editar_registro&id=' . $registro->id . '">Editar</a></td>';
            echo '<td><a href="?page=meu-plugin-admin&action=excluir_registro&id=' . $registro->id . '">Excluir</a></td>';
            echo '</tr>';
        }
       

        echo '</tbody></table>';
    } else {
        echo '<p>Nenhum registro encontrado.</p>';
    }
}
