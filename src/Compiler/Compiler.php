<?php 

namespace Compiler;

require __DIR__ . '/Tokenizer.php';
require __DIR__ . '/Parser.php';

// TODO: Napravi da se razlikuju print i printraw !!!

class Compiler {
    private $code;
    private $sql_depth;

    private function escape_string($str) {
        // Dodaj backslash ispred svakog od navodnika u stringu
        $escaped = preg_replace('/\'/', '\\\'', $str);
        // Vrati rezultat
        return $escaped;
    }

    public function __construct() {
        $this->code = '';
        $this->sql_depth = 0;
    }

    public function generate_code($node, $expected_type) {

        switch ($node->get_type()) {

            case 'NUMBER':
                return $node->get_value();

            case 'STRING':
                return '\'' . $this->escape_string($node->get_value()) . '\'';

            case 'NULL':
                return 'null';

            case 'HTML_STRING':
                return ' ?>'. $node->get_value() .'<?php ';

            case 'VARIABLE':
                if ($node->get_var_type() === 'GLOBAL')
                    return '$var->get_global(\''. $node->get_name() .'\', \''. $expected_type .'\', '. $node->get_line() .')';
                else
                    return '$var->get_query(\''. $node->get_name() .'\', \''. $expected_type .'\', '. $node->get_line() .')';
            
            case 'VARIABLE_ASSIGN':
                return '$var->set_global(\''. $node->get_name() .'\', '. $this->generate_code($node->get_value(), 'ANY') .', '. $node->get_line() .');';
            
            case 'VARIABLE_DELETE':
                return '$var->delete_global(\''. $node->get_name() .'\', '. $node->get_line() .')';

            case 'FUNCTION':
                $arguments = $node->get_arguments();
                $argument_count = count($arguments);
                $arguments_types = $node->get_argument_types();

                $code = '\''. $node->get_real_name() .'\'(';

                for ($i = 0; $i < $argument_count; $i++) {
                    $code .= $this->generate_code($arguments[$i], $arguments_types[$i]);

                    if ($i + 1 < $argument_count)
                        $code .= ', ';
                }

                return $code . ')';

            case 'PRINT':
                $code = 'echo ';
                $content = $node->get_content();
                $content_count = count($content);

                if (!$node->get_raw())
                    $code .= 'htmlspecialchars(';

                for ($i = 0; $i < $content_count; $i++) {
                    $code .= $this->generate_code($content[$i], 'ANY');

                    if ($i + 1 < $content_count)
                        $code .= ' . ';
                }

                if (!$node->get_raw())
                    $code .= ')';

                return $code . ';';

            case 'IF_ELSE':
                $code = 'if (' . $this->generate_code($node->get_condition(), 'ANY') . ') {';
                foreach ($node->get_if() as $body_node) {
                    $code .= $this->generate_code($body_node, 'ANY');
                }

                $code .= '} else {';
                foreach ($node->get_else() as $body_node) {
                    $code .= $this->generate_code($body_node, 'ANY');
                }

                return $code . '}';

            case 'OR':
                return '('. $this->generate_code($node->get_left_operator(), 'ANY') .') || ('. $this->generate_code($node->get_right_operator(), 'ANY') .')';

            case 'AND':
                return '('. $this->generate_code($node->get_left_operator(), 'ANY') .') && ('. $this->generate_code($node->get_right_operator(), 'ANY') .')';
            
            case 'IMAGE':
                return 'echo \'data:image/jpg;charset=utf8;base64,\' . base64_encode('. $this->generate_code($node->get_image(), 'ANY') .')';

            case 'FILE':
                return '$env->send_file('. $this->generate_code($node->get_file_content(), 'ANY') .', \'' . $node->get_file_name() .'\');';

            case 'SQL_QUERY':
                $code = '';
                ++$this->sql_depth;
                $err_handle = $node->get_handle_error();

                if ($err_handle) {
                    $code .= 'try { ';
                }

                $code .= '$result_set_'. $this->sql_depth . ' = $db->query(';
                $query_body = $node->get_query();
                $query_count = count($query_body);

                for ($i = 0; $i < $query_count; $i++) {
                    $code .= $this->generate_code($query_body[$i], 'ANY');

                    if ($i + 1 < $query_count)
                        $code .= ' . ';
                }
                $code .= '); if ($result_set_'. $this->sql_depth .'->num_rows === 0) { ';
                
                $empty_body = $node->get_empty();
                $empty_count = count($empty_body);

                foreach ($node->get_empty() as $empty_node) {
                    $code .= $this->generate_code($empty_node, 'ANY');
                }

                $code .= '} else { while ($result_set_'. $this->sql_depth .'_row = $result_set_'. $this->sql_depth .'->fetch_assoc()) { ';
                $code .= '$var->push_query($result_set_'. $this->sql_depth .'_row);';
                
                foreach ($node->get_body() as $body_node) {
                    $code .= $this->generate_code($body_node, 'ANY');
                }

                $code .= '$var->pop_query(); }}';
                
                if ($err_handle) {
                    $code .= '} catch (\\Errors\\SqlQueryError $e) { ';

                    foreach ($node->get_error() as $error_node) {
                        $code .= $this->generate_code($error_node, 'ANY');
                    }
                    
                    $code .= '}';
                }
                --$this->sql_depth;

                return $code;
        }
    }

    public function compile($ast) {
        $this->code = '<?php ';

        foreach ($ast->get_nodes() as $node) {
            $this->code .= $this->generate_code($node, 'ANY');
        }

        $this->code .= ' ?>';
        return $this->code;
    }

    public function get_code() {
        return $this->code;
    }
}



$test_string = file_get_contents(__DIR__ . '/__test_inputs__/cp_input_2.psql');

try {
    $tokens = tokenizer($test_string);
    //var_dump($tokens);
    $parser = new Parser;

    $ast = $parser->parse($tokens);
    //var_dump($ast);
    //echo json_encode((array)$ast);

    $compiler = new Compiler();

    $code = $compiler->compile($ast);
    echo "\n\n" . $code . "\n\n";

} catch (PandaParseError $e) {
    echo $e->getMessage() . "\n\n";
}


?>

