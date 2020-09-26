<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

global $post;
global $c_debi_theme;

$entity = new C_DEBI_Theme\Entity($post);

?>

<div class="full-item">
    <?php
        echo $c_debi_theme->blade_runner->run('Frontend.layout.entity_full', [ 'entity' => $entity, ]);
    ?>
</div>