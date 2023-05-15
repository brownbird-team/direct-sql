<?php

namespace Controller;

class PandaController {

    public function render_view($layout_name, $view_name, $args) {

        ob_start();

        require __DIR__ . '/../../views/pages/'. $view_name .'.php';
        require __DIR__ . '/../../views/pages/'. $view_name .'.properties.php';
        require __DIR__ . '/../../views/layouts/'. $layout_name .'.properties.php';

        $content = ob_get_contents();
        ob_end_clean();

        require __DIR__ . '/../../views/layouts/'. $layout_name .'.php';
    }
}