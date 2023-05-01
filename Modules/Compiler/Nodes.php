<?php

namespace Compiler\Nodes;
use \Environment\Functions\Loader;
use \Errors\PandaParseError;

// ROOT node svakog PandaSQL programa (AST-a)
class PandaProgram {
    protected $use_buffer;
    protected $program_body;
    // Inicijaliziraj varijable
    public function __construct() {
        $this->use_buffer = false;
        $this->program_body = [];
    }
    // Ubaci novi node u program
    public function push_node($node) {
        $this->program_body[] = $node;
    }
    // Dobavi node na danom indexu
    public function get_node(int $index) {
        return $this->program_body[$index];
    }
    // Dobavi broj nodeova u programu
    public function get_length() {
        return count($this->program_body);
    }
    // Postavi koristi li se buffer
    public function set_buffer(bool $buffer_state) {
        $this->use_buffer = $buffer_state;
    }
    // Dobavi koristi li se buffer
    public function get_buffer_state() {
        return $this->use_buffer;
    }
}

// Prototype of each node in the program
// all node classes are inherited from this one
class PandaNode {
    protected $node_type;
    protected $line_number;

    public function __construct(string $type, int $line) {
        $this->node_type = $type;
        $this->line_number = $line;
    }
    public function get_line() {
        return $this->line_number;
    }
    public function get_type() {
        return $this->node_type;
    }
}

// Simple number node (currently only ints are suppored)
class PandaNumber extends PandaNode {
    protected $value;

    public function __construct(int $num, int $line) {
        parent::__construct('NUMBER', $line);
        $this->value = $num;
    }
    public function get_value() {
        return $this->value;
    }
}

// Simple string node
class PandaString extends PandaNode {
    protected $value;

    public function __construct(string $str, int $line) {
        parent::__construct('STRING', $line);
        $this->value = $str;
    }
    public function get_value() {
        return $this->value;
    }
    public function get_length() {
        return strlen($this->value);
    }
}

// Simple string node
class PandaHtml extends PandaNode {
    protected $value;

    public function __construct(string $str, int $line) {
        parent::__construct('HTML_STRING', $line);
        $this->value = $str;
    }
    public function get_value() {
        return $this->value;
    }
    public function get_length() {
        return strlen($this->value);
    }
}

// Simple NULL node
class PandaNull extends PandaNode {
    public function __construct(int $line) {
        parent::__construct('NULL', $line);
    }
}

// Defined variable that contains value
class PandaVariable extends PandaNode {
    protected $name;
    protected $var_type;

    public function __construct(string $name, string $var_type, int $line) {
        parent::__construct('VARIABLE', $line);
        $this->name = $name;
        $this->var_type = $var_type;
    }
    public function get_name() {
        return $this->name;
    }
    public function get_var_type() {
        return $this->var_type;
    }
}

// Assignment of variable
class PandaVariableAssign extends PandaNode {
    protected $name;
    protected $value;

    public function __construct(string $name, $value, int $line) {
        parent::__construct('VARIABLE_ASSIGN', $line);
        $this->name = $name;
        $this->value = $value;
    }
    public function get_name() {
        return $this->name;
    }
    public function get_value() {
        return $this->value;
    }
}

// Delete this variable
class PandaVariableDelete extends PandaNode {
    protected $name;

    public function __construct(string $name, int $line) {
        parent::__construct('VARIABLE_DELETE', $line);
        $this->name = $name;
    }
    public function get_name() {
        return $this->name;
    }
}

// Call one of build in functions
class PandaFunctionCall extends PandaNode {
    protected $func_name;
    protected $func_arguments;

    public function __construct(string $name, array $arguments, int $line) {
        parent::__construct('FUNCTION', $line);

        // Ako dana funkcija postoji
        if (Loader::exists($name)) {
            // Izbroji argumente ove funkcije
            $function_arguments_length = Loader::get_arguments_count($name);
            // Ako nije dan odgovarajući broj argumenata urlaj
            if (count($arguments) !== $function_arguments_length)
                throw new PandaParseError('Wrong number of arguments for function "'. $name .'"', $line);
            // Provjeri sve argumente funkcije
            for ($i = 0; $i < $function_arguments_length; $i++) {
                // Provjeri je li tip argumenta isti kao što funkcija traži
                if ($arguments[$i]->get_type() !== Loader::get_arguments($name)[$i]) {
                    // Ako funkcija prima tip ANY kao argument idi dalje
                    if (Loader::get_arguments($name)[$i] === 'ANY')
                        continue;
                    // Ako je tip argumenta varijabla, to će biti evaluirano kod izvršavanja
                    if ($arguments[$i]->get_type() === 'VARIABLE')
                        continue;
                    // Ako je kao argument dana funkcija
                    if ($arguments[$i]->get_type() === 'FUNCTION') {
                        // Ako je return type te funkcije ANY vrati grešku (jer ako smo došli do ovdje pozvana funkcija ne traži ANY kao atribut)
                        if ($arguments[$i]->get_return_type() === 'ANY')
                            throw new PandaParseError(
                                'Function "'. $arguments[$i]->get_name() .'" returns ANY and thus cannot be used as argument of function "'. $name .'"', $line
                            );
                        // Ako funkcija ne vrača ništa nemože biti argument (nema smisla)
                        if ($arguments[$i]->get_return_type() === 'NONE')
                            throw new PandaParseError('Function that returns NONE cannot be used as argument of function "'. $name .'"', $line);
                        // Ako funkcija nema isti return type kao što se traži za argument vrati grešku
                        if ($arguments[$i]->get_return_type() !== Loader::get_arguments($name)[$i])
                            throw new PandaParseError(
                                'Return type of function "'. $arguments[$i]->get_name() .'" does not match argument type of function "'. $name .'"', $line
                            );
                        continue;
                    }

                    throw new PandaParseError('Invalid type "'. $arguments[$i]->get_type() .'" of argument '. ($i + 1) .' for function "'. $name .'"', $line);
                }
            }

            $this->func_name = $name;
            $this->func_arguments = $arguments;

        // Ako dana funkcija ne postoji
        } else {
            throw new PandaParseError('Unknown build in function with name "'. $name .'"', $line);
        }
    }
    public function get_name() {
        return $this->func_name;
    }
    public function get_real_name() {
        return Loader::get_real_name($this->func_name);
    }
    public function get_return_type() {
        return Loader::get_return_type($this->func_name);
    }
}

// Make query to MySQL database
class PandaSqlQuery extends PandaNode {
    protected $query;         // Database query string (array)
    protected $body;          // Executed for each row
    protected $empty;         // Executed if no rows are fetched for query
    protected $error;         // Executed if error occures
    protected $handle_error;  // Should error be handled with error block or throwed to user

    public function __construct(int $line) {
        parent::__construct('SQL_QUERY', $line);

        $this->query = [];
        $this->body = [];
        $this->empty = [];
        $this->error = [];
        $this->handle_error = false;
    }

    public function push_query($node) {
        $this->query[] = $node;
    }
    public function get_query() {
        return $this->query;
    }
    public function push_body($node) {
        $this->body[] = $node;
    }
    public function get_body() {
        return $this->body;
    }
    public function push_empty($node) {
        $this->empty[] = $node;
    }
    public function get_empty() {
        return $this->empty;
    }
    public function push_error($node) {
        $this->error[] = $node;
    }
    public function get_error() {
        return $this->error;
    }

    public function set_handle_error(bool $handle) {
        $this->handle_error = $handle;
    }
    public function get_handle_error() {
        return $this->handle_error;
    }
}

// Variable printed to html as image link
class PandaImage extends PandaNode {
    protected $image;

    public function __construct($image, int $line) {
        parent::__construct('IMAGE', $line);
        $this->image = $image;
    }
    public function get_image() {
        return $this->image;
    }
}

// Variable send to the user as file
// if this node exist in the tree, output buffer should be used
// so it can be discarded if sendfile command is called
class PandaFile extends PandaNode {
    protected $file;
    protected $file_name;

    public function __construct($file, string $file_name, int $line) {
        parent::__construct('FILE', $line);
        $this->file = $file;
        $this->file_name = $file_name;

    }
    public function get_file_content() {
        return $this->file;
    }
    public function get_file_name() {
        return $this->file_name;
    }
}

// If and else statement
class PandaIfElseBlock extends PandaNode {
    protected $condition;
    protected $if_body;
    protected $else_body;

    public function __construct($condition, int $line) {
        parent::__construct('IF_ELSE', $line);

        $this->condition = $condition;
        $this->if_body = [];
        $this->else_body = [];
    }
    public function push_if($node) {
        $this->if_body[] = $node;
    }
    public function get_if() {
        return $this->if_body;
    }
    public function push_else($node) {
        $this->else_body[] = $node;
    }
    public function get_else() {
        return $this->else_body;
    }
}

// Logical OR expression
class PandaLogicalOr extends PandaNode {
    protected $left;
    protected $right;

    public function __construct($left, $right, int $line) {
        parent::__construct('OR', $line);
        $this->left = $left;
        $this->right = $right;
    }
    public function get_left_operator() {
        return $this->left;
    }
    public function get_right_operator() {
        return $this->right;
    }
}

// Logical AND expression
class PandaLogicalAnd extends PandaNode {
    protected $left;
    protected $right;

    public function __construct($left, $right, int $line) {
        parent::__construct('AND', $line);
        $this->left = $left;
        $this->right = $right;
    }
    public function get_left_operator() {
        return $this->left;
    }
    public function get_right_operator() {
        return $this->right;
    }
}

// Print (echo) content to output
class PandaPrint extends PandaNode {
    protected $content;
    protected $print_raw;

    public function __construct(int $line) {
        parent::__construct('PRINT', $line);
        $this->content = [];
        $this->print_raw = false;
    }
    public function push($node) {
        if ($node->get_type() === 'FUNCTION' && Loader::get_return_type($node->get_name()) === 'NONE')
            throw new PandaParseError('Cannot print value of function that returns NONE', $this->line_number);

        $this->content[] = $node;
    }
    public function get_content() {
        return $this->content;
    }
    public function set_raw(bool $raw) {
        $this->print_raw = $raw;
    }
    public function get_raw() {
        return $this->print_raw;
    }
}

?>