<?php

namespace Compiler;
use \Errors\PandaParseError;
use \Environment\Functions\Loader;

// Commands (keywords) accepted by parser
const CMD_PRINT     = 'print';
const CMD_PRINTRAW  = 'printraw';
const CMD_IMAGE     = 'image';
const CMD_SQL       = 'sql';
const CMD_IF        = 'if';
const CMD_ELSE      = 'else';
const CMD_ENDIF     = 'endif';
const CMD_FILE      = 'file';
const CMD_SET       = 'set';
const CMD_DELETE    = 'delete';

// Podkomande naredbe SQL
const SUBCMD_SQL_QUERY    = 'query';
const SUBCMD_SQL_EMPTY    = 'empty';
const SUBCMD_SQL_ERROR    = 'error';
const SUBCMD_SQL_END      = 'end';

// Logički operatori
const OPERATOR_LOGIC_OR = 'or';
const OPERATOR_LOGIC_AND = 'and';

// Ostali keywords
const KEYWORD_NULL = 'null';

// Parse tokens into AST
class Parser {
    private $tokens;                     // Lista tokena
    private $token_count;                // Broj tokena
    private $pointer;                    // Index tokena koji se trenutno obrađuje
    private $ast;                        // Abstraktno sintaksno drvo !
    private $variables_table = [];       // Lista svih (GLOBALNIH) varijabli koje su deklarirane
    private $in_expression = false;      // Check if we already entered expression

    // Check if variable exists in variable table
    private function variable_exists(string $name) {
        return isset($this->variables_table[$name]);
    }

    // Add variable to variable table
    private function add_variable(string $name) {
        $this->variables_table[$name] = 1;
    }

    // Delete variable from variable table
    private function delete_variable(string $name) {
        unset($this->variables_table[$name]);
    }

    // Provjeri je li token funkcija
    private function token_is_function($token_index) {
        return 
            $token_index + 1 < $this->token_count 
                && $this->tokens[$token_index]->get_type() === 'NAME' 
                && $this->tokens[$token_index + 1]->get_type() === 'PARENTHESIS'
                && $this->tokens[$token_index + 1]->get_par_type() === 'LEFT';
    }

    // Provjeri ima li token vrijednost koja se može dodjeliti ili ispisati
    private function token_is_assignable($token_index) {
        $token = $this->tokens[$token_index];
        $type = $token->get_type();

        return 
            $type === 'VARIABLE' || $type === 'NUMBER' || $type === 'STRING'
                || ($type === 'PARENTHESIS' && $token->get_par_type() === 'LEFT')
                || (
                    $this->token_is_function($token_index) 
                    && Loader::exists($token->get_value()) 
                    && Loader::get_return_type($token->get_value()) !== 'NONE'
                );
    }

    // Provjeri je li trenutni token operator, ako je napravi drvo za expression
    // koristeći prethodni node (argument) i vrati ga, ako nije vrati dani node
    private function check_if_expression($current_node, $parent_type) {

        if ($this->in_expression)
            return $current_node;

        $token = $this->tokens[$this->pointer];

        if ($token->get_type() === 'NAME') {
            if ($token->get_value() === OPERATOR_LOGIC_OR) {
                ++$this->pointer;
                $this->in_expression = true;
                
                $exp = new Nodes\PandaLogicalOr($current_node, $this->walk($parent_type), $token->get_line());
                
                $this->in_expression = false;
                return $this->check_if_expression($exp, $parent_type);
            }
            if ($token->get_value() === OPERATOR_LOGIC_AND) {
                ++$this->pointer;
                $this->in_expression = true;
                
                $exp = new Nodes\PandaLogicalAnd($current_node, $this->walk($parent_type), $token->get_line());
                
                $this->in_expression = false;
                return $this->check_if_expression($exp, $parent_type);
            }
        }

        return $current_node;
    }

    // Check if current token is end of command
    private function check_command_end() {
        $token = $this->tokens[$this->pointer++];
        // Throw error if current token is not end of this command
        if ($token->get_type() !== 'COMMAND_END')
            throw new PandaParseError('Expected %} but found unexpected token', $token->get_line());
    }

    // Rekurzivna funkcija koja kreira AST od tokena
    private function walk($parent_type) {

        $token = $this->tokens[$this->pointer];

        // Ako je ovo kraj programa vrati null
        if ($token->get_type() === 'END') {
            return null;
        }

        // Ako je token komad HTML-a
        if ($token->get_type() === 'HTML_STRING') {
            ++$this->pointer;
            return new Nodes\PandaHtml($token->get_value(), $token->get_line());
        }

        // Ako je tip tokena NAME
        if ($token->get_type() === 'NAME') {
            // Ako se nalazi u najgornjemu nodu (PROGRAM) znači da se radi o naredbi a ne funkciji
            if ($parent_type === 'PROGRAM' || $parent_type === 'SQL_QUERY' || $parent_type === 'IF_ELSE') {
                // Koja naredba je navedena
                $cmd = $token->get_value();

                // Handle commands print and printraw
                if ($cmd === CMD_PRINT || $cmd === CMD_PRINTRAW) {
                    
                    $print_node = new Nodes\PandaPrint($token->get_line());

                    if ($cmd === CMD_PRINTRAW)
                        $print_node->set_raw(true);

                    ++$this->pointer;

                    while (true) {
                        $token = $this->tokens[$this->pointer];
                        // Idi dok su tokeni koje se može ispisati (imaju vrijednost)
                        if ($this->token_is_assignable($this->pointer)) {
                            $print_node->push($this->walk($print_node->get_type()));
                            continue;
                        }
                        $this->check_command_end();
                        
                        // Ako nije dan niti jedan token koji ima vrijednost vrati grešku
                        if (count($print_node->get_content()) === 0)
                            throw new PandaParseError('Cannot print nothingness', $print_node->get_line());

                        return $print_node;
                    }
                }

                // Handle all SQL subcommands
                if ($cmd === CMD_SQL) {

                    $sql_node = new Nodes\PandaSqlQuery($token->get_line());
                    $sub_cmd = $this->tokens[++$this->pointer];

                    if ($sub_cmd->get_type() !== 'NAME' || $sub_cmd->get_value() !== SUBCMD_SQL_QUERY)
                        throw new PandaParseError('Unexpected SQL subcommand found', $token->get_line());

                    $this->pointer += 2;

                    while (true) {
                        $token = $this->tokens[$this->pointer];

                        if ($token->get_type() === 'SQL_STRING' || $token->get_type() === 'VARIABLE') {
                            $sql_node->push_query($this->walk('SQL_QUERY_QUERY'));
                            continue;
                        }

                        if ($token->get_type() === 'SQL_END') {
                            ++$this->pointer;
                            $this->check_command_end();
                            break;
                        }
                        
                        throw new PandaParseError('SQL query can only contain SQL code and variables', $token->get_line());
                    }

                    $sql_area = 'BODY';

                    while (true) {
                        $token = $this->tokens[$this->pointer];

                        if ($token->get_type() === 'NAME' && $token->get_value() === 'sql') {
                            $sub_cmd = $this->tokens[$this->pointer + 1]->get_value();

                            if ($sub_cmd === SUBCMD_SQL_EMPTY) {
                                $this->pointer += 2;
                                $sql_area = 'EMPTY';
                                $this->check_command_end();
                            }
                            else if ($sub_cmd === SUBCMD_SQL_ERROR) {
                                $this->pointer += 2;
                                $sql_area = 'ERROR';
                                $sql_node->set_handle_error(true);
                                $this->check_command_end();
                            }
                            else if ($sub_cmd === SUBCMD_SQL_END) {
                                $this->pointer += 2;
                                $this->check_command_end();
                                break;
                            }
                        }

                        if ($token->get_type() === 'END')
                            throw new PandaParseError('All SQL blocks need to end with "{% sql end %}", never-ending block found', $sql_node->get_line());

                        if ($sql_area === 'BODY')
                            $sql_node->push_body($this->walk($sql_node->get_type()));
                        if ($sql_area === 'EMPTY')
                            $sql_node->push_empty($this->walk($sql_node->get_type()));
                        if ($sql_area === 'ERROR')
                            $sql_node->push_error($this->walk($sql_node->get_type()));
                    }

                    return $sql_node;
                }

                // Handle if-else statements
                if ($cmd === CMD_IF) {

                    ++$this->pointer;
                    $condition_node = $this->walk('IF_ELSE_CONDITION');
                    $this->check_command_end();

                    $if_area = 'IF';
                    $if_node = new Nodes\PandaIfElseBlock($condition_node, $token->get_line());

                    while (true) {
                        $token = $this->tokens[$this->pointer];

                        if ($token->get_type() === 'NAME') {
                            if ($token->get_value() === CMD_ELSE) {
                                ++$this->pointer;
                                $if_area = 'ELSE';
                                $this->check_command_end();
                            }
                            if ($token->get_value() === CMD_ENDIF) {
                                ++$this->pointer;
                                $this->check_command_end();
                                break;
                            }
                        }

                        if ($if_area === 'IF')
                            $if_node->push_if($this->walk($if_node->get_type()));
                        if ($if_area === 'ELSE')
                            $if_node->push_else($this->walk($if_node->get_type()));
                    }

                    return $if_node;
                }

                // Handle file sending command
                if ($cmd === CMD_FILE) {
                    ++$this->pointer;

                    if (!$this->token_is_assignable($this->pointer))
                        throw new PandaParseError('Expected assignable token, cannot send unassignable token as file', $token->get_line());

                    $file_content = $this->walk('FILE');
                    $file_name = $this->tokens[$this->pointer++];

                    if ($file_name->get_type() !== 'STRING')
                        throw new PandaParseError('Filename must be spacified as STRING', $token->get_line());
                    
                    $file_node = new Nodes\PandaFile($file_content, $file_name->get_value(), $token->get_line());
                    $this->check_command_end();
                    return $file_node;
                }

                // Handle image link generation
                if ($cmd === CMD_IMAGE) {
                    ++$this->pointer;

                    if (!$this->token_is_assignable($this->pointer))
                        throw new PandaParseError('Expected assignable token, cannot print unassignable token as image link', $token->get_line());
                    
                    $image_node = new Nodes\PandaImage($this->walk('IMAGE'), $token->get_line());
                    $this->check_command_end();
                    return $image_node;
                }

                // Handle variable creation
                if ($cmd === CMD_SET) {

                    $var = $this->tokens[++$this->pointer];

                    if ($var->get_type() !== 'VARIABLE')
                        throw new PandaParseError('You cannot assign value to anything but variable', $token->get_line());
                    if ($var->get_var_type() === 'QUERY')
                        throw new PandaParseError('You cannot assign value to QUERY variable', $token->get_line());

                    $content = $this->tokens[++$this->pointer];

                    if (!$this->token_is_assignable($this->pointer))
                        throw new PandaParseError('Expected assignable token, cannot assign unassignable token to variable', $token->get_line());
                    
                    ++$this->pointer;
                    $this->check_command_end();
                    $this->add_variable($var->get_value());
                    return new Nodes\PandaVariableAssign($var->get_value(), $content, $token->get_line());
                }

                // Handle variable deletion
                if ($cmd === CMD_DELETE) {

                    $var = $this->tokens[++$this->pointer];

                    if ($var->get_type() !== 'VARIABLE')
                        throw new PandaParseError('You cannot delete anything but variable', $token->get_line());
                    if ($var->get_var_type() === 'QUERY')
                        throw new PandaParseError('You cannot delete QUERY variable', $token->get_line());
                    if (!$this->variable_exists($var->get_value()))
                        throw new PandaParseError('Cannot delete variable $'. $var->get_value() .' (variable is not set)', $token->get_line());
                    
                    ++$this->pointer;
                    $this->check_command_end();
                    $this->delete_variable($var->get_value());
                    return new Nodes\PandaVariableDelete($var->get_value(), $token->get_line());
                }

            // Ako se nalazi bilo gdje drugdje trebala bi biti riječ o funkciji (osim ak nisam neš fulo)
            } else {
                // Za svaki slučaj provjeri jel funkcija
                if ($this->token_is_function($this->pointer)) {
                    // Napravi array za argumente i spremi token funkcije za kasnije
                    $attributes = [];
                    $func_token = $this->tokens[$this->pointer];
                    // Prebaci se na prvi token unutar zagrada
                    $this->pointer += 2;

                    // Ako smo pročitali sve tokene završi
                    while (true) {
                        $token = $this->tokens[$this->pointer];

                        if ($this->token_is_assignable($this->pointer)) {
                            $previous_expression_state = $this->in_expression;
                            $this->in_expression = false;

                            $attributes[] = $this->walk('FUNCTION');

                            $this->in_expression = $previous_expression_state;
                            continue;
                        }
                        if ($token->get_type() === 'PARENTHESIS' && $token->get_par_type() === 'RIGHT') {
                            ++$this->pointer;
                            return $this->check_if_expression(new Nodes\PandaFunctionCall($func_token->get_value(), $attributes, $func_token->get_line()), $parent_type);
                        }
                        throw new PandaParseError('Unexpected token near function '. $func_token->get_value() .'(...), expected ")"', $func_token->get_line());
                    }
                }
                // Ako nije funkcija mogo bi bit null
                if ($token->get_value() === KEYWORD_NULL) {
                    ++$this->pointer;
                    return $this->check_if_expression(new Nodes\PandaNull($token->get_line()), $parent_type);
                }
                // Ako nije funkcija ni null pusti da baci unexpected token na kraju ...
            }
        }

        // Svi tipovi tokena odavde prema dolje nisu naredbe, stoga nebi trebali postojati
        // na početku naredbe nakon {% znaka jer se tamo očekuje naredba
        if ($parent_type === 'PROGRAM' || $parent_type === 'SQL_QUERY' || $parent_type === 'IF_ELSE') {
            var_dump($token);
            throw new PandaParseError('You must start command with valid command name', $token->get_line());
        }

        // Ako je token otvorena zagrada
        if ($token->get_type() === 'PARENTHESIS' && $token->get_par_type() === 'LEFT') {
            $start_line = $token->get_line();
            $previous_expression_state = $this->in_expression;

            $this->in_expression = false;
            ++$this->pointer;
            $par_content = $this->walk($token->get_type());

            $token = $this->tokens[$this->pointer];
            if ($token->get_type() !== 'PARENTHESIS' || $token->get_par_type() !== 'RIGHT')
                throw new PandaParseError('Failed to find matcing ")" for "(" token', $start_line);
            
            ++$this->pointer;
            $this->in_expression = $previous_expression_state;
            return $par_content;
        }

        // Ako je tip tokena number, vrati number node
        if ($token->get_type() === 'NUMBER') {
            ++$this->pointer;
            return $this->check_if_expression(new Nodes\PandaNumber($token->get_value(), $token->get_line()), $parent_type);
        }

        // Ako je tip tokena string, vrati seting node
        if ($token->get_type() === 'STRING') {
            ++$this->pointer;
            return $this->check_if_expression(new Nodes\PandaString($token->get_value(), $token->get_line()), $parent_type);
        }

        // Ako je tip tokena varijabla, vrati varijabla node
        if ($token->get_type() === 'VARIABLE') {
            ++$this->pointer;

            // Ako dana globalna varijabla ne postoji vrati grešku
            if ($token->get_var_type() === 'GLOBAL' && !$this->variable_exists($token->get_value()))
                throw new PandaParseError('Trying to access value of non-existent GLOBAL variable $'. $token->get_value(), $token->get_line());

            return $this->check_if_expression(new Nodes\PandaVariable($token->get_value(), $token->get_var_type(), $token->get_line()), $parent_type);
        }

        // Ako je token SQL dodaj ga ko sql string node
        if ($token->get_type() === 'SQL_STRING') {
            ++$this->pointer;
            return new Nodes\PandaSqlString($token->get_value(), $token->get_line());
        }
        
        // Ako token ne odgovara niti jednom od poznatih vrati grešku
        throw new PandaParseError('Unexpected token of type "'. $token->get_type() .'" found', $token->get_line());
    }

    // Kreiraj instancu klase program i započni parasnje
    public function parse($tokens) {
        $this->pointer = 0;
        $this->tokens = $tokens;
        $this->token_count = count($tokens);
        $this->ast = new Nodes\PandaProgram();
        $this->variables_table = [];

        while ($node = $this->walk('PROGRAM')) {
            $this->ast->push_node($node);
        }

        return $this->ast;
    }
};

?>