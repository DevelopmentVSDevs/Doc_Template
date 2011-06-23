<?php

require_once (TEMPLATE_LITE_DIR . "class.compiler.php");

class Smarty_Compiler extends Template_Lite_Compiler {
    
    function Smarty_Compiler() {
        $this->Template_Lite_compiler();
    }
    

    // This will be moved into the Template Lite When We Fork XXX    
    function _compile_file($file_contents) {
        $ldq = preg_quote($this->left_delimiter);
        $rdq = preg_quote($this->right_delimiter);
        $_match = array(); // a temp variable for the current regex match
        $tags = array(); // all original tags
        $text = array(); // all original text
        $compiled_text = '<?php /* ' . $this->_version . ' ' . strftime("%Y-%m-%d %H:%M:%S %Z") . ' */ ?>' . "\n\n"; // stores the compiled result
        $compiled_tags = array(); // all tags and stuff
        
        $this->_require_stack = array();
        
        $this->_load_filters();
        
        if (count($this->_plugins['prefilter']) > 0) {
            foreach($this->_plugins['prefilter'] as $function) {
                if ($function === false) {
                    continue;
                }
                if (is_array($function) && count($function) == 2) {
                    if (method_exists($function[0], $function[1])) {
                        $file_contents = call_user_func($function, $file_contents, $this);
                    }
                } else {
                    $file_contents = $function($file_contents, $this);
                }
            }
        }
        
        // remove all comments
        $file_contents = preg_replace("!{$ldq}\*.*?\*{$rdq}!se", "", $file_contents);
        
        // replace all php start and end tags
        $file_contents = preg_replace('%(<\?(?!php|=|$))%i', '<?php echo \'\\1\'?>' . "\n", $file_contents);
        
        // remove literal blocks
        preg_match_all("!{$ldq}\s*literal\s*{$rdq}(.*?){$ldq}\s*/literal\s*{$rdq}!s", $file_contents, $_match);
        $this->_literal = $_match[1];
        $file_contents = preg_replace("!{$ldq}\s*literal\s*{$rdq}(.*?){$ldq}\s*/literal\s*{$rdq}!s", stripslashes($ldq . "literal" . $rdq), $file_contents);
        
        // remove php blocks
        preg_match_all("!{$ldq}\s*php\s*{$rdq}(.*?){$ldq}\s*/php\s*{$rdq}!s", $file_contents, $_match);
        $this->_php_blocks = $_match[1];
        $file_contents = preg_replace("!{$ldq}\s*php\s*{$rdq}(.*?){$ldq}\s*/php\s*{$rdq}!s", stripslashes($ldq . "php" . $rdq), $file_contents);
        
        // gather all template tags
        preg_match_all("!{$ldq}\s*(.*?)\s*{$rdq}!s", $file_contents, $_match);
        $tags = $_match[1];
        
        // put all of the non-template tag text blocks into an array, using the template tags as delimiters
        $text = preg_split("!{$ldq}.*?{$rdq}!s", $file_contents);
        
        // compile template tags
        $count_tags = count($tags);
        for ($i = 0, $for_max = $count_tags;$i < $for_max;$i++) {
            $this->_linenum+= substr_count($text[$i], "\n");
            $compiled_tags[] = $this->_compile_tag($tags[$i]);
            $this->_linenum+= substr_count($tags[$i], "\n");
        }
        
        // build the compiled template by replacing and interleaving text blocks and compiled tags
        $count_compiled_tags = count($compiled_tags);
        for ($i = 0, $for_max = $count_compiled_tags;$i < $for_max;$i++) {
            if ($compiled_tags[$i] == '') {
                $text[$i + 1] = preg_replace('~^(\r\n|\r|\n)~', '', $text[$i + 1]);
            }
            $compiled_text.= $text[$i] . $compiled_tags[$i];
        }
        $compiled_text.= $text[$i];
        
        foreach($this->_require_stack as $key => $value) {
            $compiled_text = '<?php require_once(\'' . $this->_get_plugin_dir($key) . $key . '\'); $this->register_' . $value[0] . '("' . $value[1] . '", "' . $value[2] . '"); ?>' . $compiled_text;
        }
        
        // remove unnecessary close/open tags
        $compiled_text = preg_replace('!\?>\n?<\?php!', '', $compiled_text);
        
        if (count($this->_plugins['postfilter']) > 0) {
            foreach($this->_plugins['postfilter'] as $function) {
                if ($function === false) {
                    continue;
                }
                if (is_array($function) && count($function) == 2) {
                    if (method_exists($function[0], $function[1])) {
                        $file_contents = call_user_func($function, $file_contents, $this);
                    }
                } else {
                    $file_contents = $function($file_contents, $this);
                }
            }
        }
        
        return $compiled_text;
    }
    
    
    // This will be moved into the Template Lite When We Fork XXX    
    function _load_filters() {
        if (count($this->_plugins['prefilter']) > 0) {
            foreach($this->_plugins['prefilter'] as $filter_name => $prefilter) {
                if (is_array($prefilter)) {

                    if (count($prefilter) == 2) {
                        if (!method_exists($prefilter[0], $prefilter[1])) {
                            @include_once ($this->_get_plugin_dir("prefilter." . $filter_name . ".php") . "prefilter." . $filter_name . ".php");
                        }
                    }
                    
                    
                } else if (!function_exists($prefilter)) {
                    @include_once ($this->_get_plugin_dir("prefilter." . $filter_name . ".php") . "prefilter." . $filter_name . ".php");
                }
            }
        }
        if (count($this->_plugins['postfilter']) > 0) {
            foreach($this->_plugins['postfilter'] as $filter_name => $postfilter) {
                if (is_array($postfilter)) {
                    
                    if (count($postfilter) == 2) {
                        if (!method_exists($postfilter[0], $postfilter[1])) {
                            @include_once ($this->_get_plugin_dir("postfilter." . $filter_name . ".php") . "postfilter." . $filter_name . ".php");
                        }
                    }
                    
                } else if (!function_exists($postfilter)) {
                    @include_once ($this->_get_plugin_dir("postfilter." . $filter_name . ".php") . "postfilter." . $filter_name . ".php");
                }
            }
        }
    }
    
    
    // This will be moved into the Template Lite When We Fork XXX    
    function _get_plugin_dir($plugin_name) {
        //    echo "_get_plugin_dir ".$plugin_name."<br>";
        static $_path_array = null;
        
        $plugin_dir_path = "";
        $_plugin_dir_list = is_array($this->plugins_dir) ? $this->plugins_dir : (array)$this->plugins_dir;
        foreach($_plugin_dir_list as $_plugin_dir) {
            if (!preg_match("/^([\/\\\\]|[a-zA-Z]:[\/\\\\])/", $_plugin_dir)) {
                // path is relative
                if (file_exists(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $_plugin_dir . DIRECTORY_SEPARATOR . $plugin_name)) {
                    $plugin_dir_path = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $_plugin_dir . DIRECTORY_SEPARATOR;
                    break;
                }
            } else {
                foreach($_plugin_dir_list as $_plugin_dir) {
                    
                    if (file_exists($_plugin_dir . DIRECTORY_SEPARATOR . $plugin_name)) {
                        //echo $_plugin_dir . DIRECTORY_SEPARATOR . $plugin_name . "-2<br>";
                        $plugin_dir_path = $_plugin_dir . DIRECTORY_SEPARATOR;
                        break;
                    }
                }
                
                
                // path is absolute
                if (!isset($_path_array)) {
                    $_ini_include_path = ini_get('include_path');
                    
                    if (strstr($_ini_include_path, ';')) {
                        // windows pathnames
                        $_path_array = explode(';', $_ini_include_path);
                    } else {
                        $_path_array = explode(':', $_ini_include_path);
                    }
                }
                
                if (!in_array($_plugin_dir, $_path_array)) {
                    array_unshift($_path_array, $_plugin_dir);
                }
                
                foreach($_path_array as $_include_path) {
                    if (file_exists($_include_path . DIRECTORY_SEPARATOR . $plugin_name)) {
                        $plugin_dir_path = $_include_path . DIRECTORY_SEPARATOR;
                        break 2;
                    }
                }
            }
        }
        return $plugin_dir_path;
    }
    
    // This will be moved into the Template Lite When We Fork XXX    
    function _plugin_exists($function, $type) {
        // check for object functions
        if (isset($this->_plugins[$type][$function]) && is_array($this->_plugins[$type][$function]) && is_object($this->_plugins[$type][$function][0]) && method_exists($this->_plugins[$type][$function][0], $this->_plugins[$type][$function][1])) {
            return '$this->_plugins[\'' . $type . '\'][\'' . $function . '\'][0]->' . $this->_plugins[$type][$function][1];
        }
        // check for standard functions
        if (isset($this->_plugins[$type][$function]) && function_exists($this->_plugins[$type][$function])) {
            return $this->_plugins[$type][$function];
        }
        
        
        // check for a plugin in the plugin directory
        //echo $this->_get_plugin_dir($type . '.' . $function . '.php') . $type . '.' . $function . '.php'."<br>";
        if (file_exists($this->_get_plugin_dir($type . '.' . $function . '.php') . $type . '.' . $function . '.php')) {
            require_once ($this->_get_plugin_dir($type . '.' . $function . '.php') . $type . '.' . $function . '.php');
            if (function_exists('tpl_' . $type . '_' . $function)) {
                $this->_require_stack[$type . '.' . $function . '.php'] = array($type, $function, 'tpl_' . $type . '_' . $function);
                return ('tpl_' . $type . '_' . $function);
            }
            if (function_exists('smarty_' . $type . '_' . $function)) {
                $this->_require_stack[$type . '.' . $function . '.php'] = array($type, $function, 'tpl_' . $type . '_' . $function);
                return ('smarty_' . $type . '_' . $function);
            }
            
        }
        return false;
    }
    
    // This will be moved into the Template Lite When We Fork XXX    
    function _compile_variable($variable)
    {
        $_result    = "";

        // remove the $
        $variable = substr($variable, 1);

        // get [foo] and .foo and (...) pieces
        preg_match_all('!(?:^\w+)|(?:' . $this->_var_bracket_regexp . ')|\.\$?\w+|\S+!', $variable, $_match);
        $variable = $_match[0];
        $var_name = array_shift($variable);

        if ($var_name == $this->reserved_template_varname)
        {
            if ($variable[0]{0} == '[' || $variable[0]{0} == '.')
            {
                $find = array("[", "]", ".");
                switch(strtoupper(str_replace($find, "", $variable[0])))
                {
                    case 'GET':
                        $_result = "\$_GET";
                        break;
                    case 'POST':
                        $_result = "\$_POST";
                        break;
                    case 'COOKIE':
                        $_result = "\$_COOKIE";
                        break;
                    case 'ENV':
                        $_result = "\$_ENV";
                        break;
                    case 'SERVER':
                        $_result = "\$_SERVER";
                        break;
                    case 'SESSION':
                        $_result = "\$_SESSION";
                        break;
                    case 'NOW':
                        $_result = "time()";
                        break;
                    case 'SECTION':
                        $_result = "\$this->_sections";
                        break;
                    case 'LDELIM':
                        $_result = "\$this->left_delimiter";
                        break;
                    case 'RDELIM':
                        $_result = "\$this->right_delimiter";
                        break;
                    case 'VERSION':
                        $_result = "\$this->_version";
                        break;
                    case 'CONFIG':
                        $_result = "\$this->_confs";
                        break;
                    case 'TEMPLATE':
                        $_result = "\$this->_file";
                        break;
                    case 'CONST':
                        $constant = str_replace($find, "", $_match[0][2]);
                        $_result = "constant('$constant')";
                        $variable = array();
                        break;
                    default:
                        $_var_name = str_replace($find, "", $variable[0]);
                        $_result = "\$this->_templatelite_vars['$_var_name']";
                        break;
                }
                array_shift($variable);
            }
            else
            {
                $this->trigger_error('$' . $var_name.implode('', $variable) . ' is an invalid $templatelite reference', E_USER_ERROR, __FILE__, __LINE__);
            }
        }
        else
        {
            $_result = "@\$this->_vars['$var_name']";
        }

        foreach ($variable as $var)
        {
            if ($var{0} == '[')
            {
                $var = substr($var, 1, -1);
                if (is_numeric($var))
                {
                    $_result .= "[$var]";
                }
                elseif ($var{0} == '$')
                {
                    $_result .= "[" . $this->_compile_variable($var) . "]";
                }
                elseif ($var{0} == '#')
                {
                    $_result .= "[" . $this->_compile_config($var) . "]";
                }
                else
                {
//                    $_result .= "['$var']";
                    $parts = explode('.', $var);
                    $section = $parts[0];
                    $section_prop = isset($parts[1]) ? $parts[1] : 'index';
                    $_result .= "[\$this->_sections['$section']['$section_prop']]";
                }
            }
            else if ($var{0} == '.')
            {
                   if ($var{1} == '$')
                {
                       $_result .= "[\$this->_TPL['" . substr($var, 2) . "']]";
                }
                   else
                {
                       $_result .= "['" . substr($var, 1) . "']";
                }
            }
            else if (substr($var,0,2) == '->')
            {
                if(substr($var,2,2) == '__')
                {
                    $this->trigger_error('call to internal object members is not allowed', E_USER_ERROR, __FILE__, __LINE__);
                }
                else if (substr($var, 2, 1) == '$')
                {
                    $_output .= '->{(($var=$this->_TPL[\''.substr($var,3).'\']) && substr($var,0,2)!=\'__\') ? $_var : $this->trigger_error("cannot access property \\"$var\\"")}';
                }
            }
            else
            {
                $this->trigger_error('$' . $var_name.implode('', $variable) . ' is an invalid reference', E_USER_ERROR, __FILE__, __LINE__);
            }
        }
        return $_result;
    }
    
    
    
    
}
?>