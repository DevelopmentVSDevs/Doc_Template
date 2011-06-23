<?php

if (!defined('SMARTY_DIR')) {
    define('SMARTY_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);
}

// If this isn't specifically defined it probably is up one level then into the src directory ../src
if (!defined('TEMPLATE_LITE_DIR')) {
    define("TEMPLATE_LITE_DIR", realpath(SMARTY_DIR . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src') . DIRECTORY_SEPARATOR);
}

require_once (TEMPLATE_LITE_DIR . "class.template.php");

class Smarty extends Template_Lite {
    
    var $compiler_class = 'Smarty_Compiler';
    var $compiler_file = 'Smarty_Compiler.class.php';
    
    function Smarty() {
        $this->Template_Lite();
        
        $this->compiler_file = realpath(TEMPLATE_LITE_DIR . '../libs/Smarty_Compiler.class.php');
        $this->registerFilter('pre', array($this, 'smarty_prefilter_literalScriptAndStyle'));
        
    }
    
    function register_prefilter($function) {
        $_name = (is_array($function)) ? $function[1] : $function;
        $this->_plugins['prefilter'][$_name] = $function;
    }
    
    function register_postfilter($function) {
        $_name = (is_array($function)) ? $function[1] : $function;
        $this->_plugins['postfilter'][$_name] = $function;
    }
    
    function register_outputfilter($function) {
        $_name = (is_array($function)) ? $function[1] : $function;
        $this->_plugins['outputfilter'][$_name] = $function;
    }
    
    function _get_vars($var) {
        if (isset($this->_vars[$var])) return ($this->_vars[$var]);
        else return (false);
    }
    
    
    /*
    
    function isCached($template, $cache_id = null, $compile_id = null) {
        
    } */
    
    function setTemplateDir($template_dir) {
        $this->template_dir = $template_dir;
    }
    function addTemplateDir($template_dir) {
        echo "Template Lite: Unsupported " . __FILE__ . '-' . __LINE__;
        exit(0);
    }
    
    function templateExists($resource_name) {
        return (parent::template_exists($resource_name));
    }
    
    function loadPlugin($plugin_name, $check = true) {
        
        
        if ($check && (is_callable($plugin_name) || class_exists($plugin_name, false))) return true;
        // Plugin name is expected to be: Smarty_[Type]_[Name]
        $_plugin_name = strtolower($plugin_name);
        $_name_parts = explode('_', $_plugin_name, 3);
        
        if (count($_name_parts) < 3 || $_name_parts[0] !== 'smarty') {
            return false;
        }
        
        
        // if type is "internal", get plugin from sysplugins
        if ($_name_parts[1] == 'internal') {
            $file = SMARTY_SYSPLUGINS_DIR . $_plugin_name . '.php';
            if (file_exists($file)) {
                require_once ($file);
                return $file;
            } else {
                return false;
            }
        }
        // plugin filename is expected to be: [type].[name].php
        $_plugin_filename = "{$_name_parts[1]}.{$_name_parts[2]}.php";
        // loop through plugin dirs and find the plugin
        foreach((array)$this->plugins_dir as $_plugin_dir) {
            if (strpos('/\\', substr($_plugin_dir, -1)) === false) {
                $_plugin_dir.= DS;
            }
            $file = $_plugin_dir . $_plugin_filename;
            if (file_exists($file)) {
                require_once ($file);
                return $file;
            }
        }
        // no plugin loaded
        return false;
        
    }
    
    
    function registerFilter($type, $name) {
        if ($type == "pre") {
            $this->register_prefilter($name);
        }
        if ($type == "post") {
            $this->register_postfilter($name);
        }
        if ($type == "output") {
            $this->register_outputfilter($name);
        }
    }
    
    function loadFilter($type, $name) {
        $this->load_filter($type, $name);
    }
    
    
    function addPluginsDir($plugins_dir) {
        //if (!preg_match("/^([\/\\\\]|[a-zA-Z]:[\/\\\\])/", $plugins_dir)) {
        if (defined("SERVERROOT")) {
            $this->plugins_dir[] = sprintf("%s%s", SERVERROOT, $plugins_dir);
        } else {
            $this->plugins_dir[] = $plugins_dir;
        }
    }
    
    function assign($tpl_var, $value = null, $nocache = false) {
        if ($nocache !== false) {
            echo "Template Lite: Unsupported " . __FILE__ . '-' . __LINE__;
            exit(0);
        }
        
        return (parent::assign($tpl_var, $value));
    }
    function assignByRef($tpl_var, &$value, $nocache = false) {
        if ($nocache !== false) {
            echo "Template Lite: Unsupported " . __FILE__ . '-' . __LINE__;
            exit(0);
        }
        return (parent::assign_by_ref($tpl_var, $value));
        
        // function assign_by_ref($key, $value = null)
        
    }
    function append($tpl_var, $value = null, $merge = false, $nocache = false) {
        if ($nocache !== false) {
            echo "Template Lite: Unsupported " . __FILE__ . '-' . __LINE__;
            exit(0);
        }
        return (parent::append($tpl_var, $value, $merge));
    }
    function appendByRef($tpl_var, &$value, $merge = false) {
        if ($nocache !== false) {
            echo "Template Lite: Unsupported " . __FILE__ . '-' . __LINE__;
            exit(0);
        }
        return (parent::append_by_ref($tpl_var, $value, $merge));
        
    }
    function clearAssign($tpl_var) {
        return (parent::clear_assign($tpl_var));
        
    }
    function clearAllAssign() {
        return (parent::clear_all_assign());
        
    }
    
    
    
    
    function &getTemplateVars($varname = null, $_ptr = null, $search_parents = true) {
        // function &get_template_vars($key = null)
        if ($_ptr != null && $search_parents !== true) {
            echo "Template Lite: Unsupported " . __FILE__ . '-' . __LINE__;
            exit(0);
        }
        return (parent::get_template_vars($varname = null));
        
    }
    
    
    
    function setCacheId($id) {
        $this->cache_id = $id;
    }
    
    function getCacheId() {
        return ($this->cache_id);
    }
    
    function setCaching($status) {
        $this->cache = $status;
    }
    
    function getCaching() {
        return ($this->cache);
    }
    
    
    function setCacheLifetime($cache_time) {
        $this->cache_lifetime = $cache_time;
        
    }
    
    function getCacheLifetime() {
        return ($this->cache_lifetime);
    }
    
    
    function setCompileDir($dir) {
        $this->compile_dir = $dir;
    }
    
    function getCompileDir() {
        return ($this->compile_dir);
    }
    
    
    function setCacheDir($dir) {
        $this->cache_dir = $dir;
    }
    
    function getCacheDir() {
        return ($this->cache_dir);
    }
    
    
    
    function getPluginsDir() {
        return ($this->plugins_dir);
    }
    
    function setPluginsDir($dir) {
        $this->plugins_dir = $dir;
    }
    
    // To further emulate Smarty3 if there are no literal's already we will literal the javascript to keep it being caught by the parser
    function smarty_prefilter_literalScriptAndStyle($tpl_source, $smarty) {
        
        if (strpos($tpl_source, $this->left_delimiter . "literal" . $this->right_delimiter) === false) {
            $pattern[] = '~<script\b(?![^>]*smarty)(.*)</script>~siU';
            $replace[] = $this->left_delimiter . 'literal' . $this->right_delimiter . '<script$1 $2</script>' . $this->left_delimiter . ' /literal' . $this->right_delimiter;
            $pattern[] = '~<style\b(?![^>]*smarty)>(.*)</style>~siU';
            $replace[] = $this->left_delimiter . 'literal' . $this->right_delimiter . '<style$1>$2</style>' . $this->left_delimiter . '/literal' . $this->right_delimiter;
            return preg_replace($pattern, $replace, $tpl_source);
        } else {
            return ($tpl_source);
        }
    }
    
    
    function clearAllCache($exp_time = null, $type = null) {
        echo "Template Lite: Unsupported " . __FILE__ . '-' . __LINE__;
        exit(0);
    }
    function clearCache($template_name, $cache_id = null, $compile_id = null, $exp_time = null, $type = null) {
        echo "Template Lite: Unsupported " . __FILE__ . '-' . __LINE__;
        exit(0);
    }
    
    
    function registerResource($resource_type, $function_names) {
        echo "Template Lite: Unsupported " . __FILE__ . '-' . __LINE__;
        exit(0);
    }
    function registerDefaultPluginHandler($function_name) {
        echo "Template Lite: Unsupported " . __FILE__ . '-' . __LINE__;
        exit(0);
        
    }
    function registerDefaultTemplateHandler($function_name) {
        echo "Template Lite: Unsupported " . __FILE__ . '-' . __LINE__;
        exit(0);
        
    }
    
    function registerPlugin($type, $tag, $callback, $cacheable = true, $cache_attr = array()) {
        echo "Template Lite: Unsupported " . __FILE__ . '-' . __LINE__;
        exit(0);
    }
    
    
    function unregisterPlugin($type, $tag) {
        echo "Template Lite: Unsupported " . __FILE__ . '-' . __LINE__;
        exit(0);
        
    }
    function unregisterObject($object_name) {
        echo "Template Lite: Unsupported " . __FILE__ . '-' . __LINE__;
        exit(0);
        
    }
    function unregisterFilter($type, $function_name) {
        echo "Template Lite: Unsupported " . __FILE__ . '-' . __LINE__;
        exit(0);
    }
    function unregisterResource($resource_type) {
        echo "Template Lite: Unsupported " . __FILE__ . '-' . __LINE__;
        exit(0);
    }
    
    function compileAllTemplates($extention = '.tpl', $force_compile = false, $time_limit = 0, $max_errors = null) {
        echo "Template Lite: Unsupported " . __FILE__ . '-' . __LINE__;
        exit(0);
    }
    function clearCompiledTemplate($resource_name = null, $compile_id = null, $exp_time = null) {
        echo "Template Lite: Unsupported " . __FILE__ . '-' . __LINE__;
        exit(0);
    }
    
    function registerObject($object_name, $object_impl, $allowed = array(), $smarty_args = true, $block_methods = array()) {
        echo "Template Lite: Unsupported " . __FILE__ . '-' . __LINE__;
        exit(0);
    }
    
    
    function setExceptionHandler($handler) {
        echo "Template Lite: Unsupported " . __FILE__ . '-' . __LINE__;
        exit(0);
    }
    
    
    function assignGlobal($varname, $value = null, $nocache = false) {
        echo "Template Lite: Unsupported " . __FILE__ . '-' . __LINE__;
        exit(0);
    }
    
    
    function getGlobal($varname = null) {
        echo "Template Lite: Unsupported " . __FILE__ . '-' . __LINE__;
        exit(0);
        
    }
    function getRegisteredObject($name) {
        echo "Template Lite: Unsupported " . __FILE__ . '-' . __LINE__;
        exit(0);
        
    }
    function getDebugTemplate() {
        echo "Template Lite: Unsupported " . __FILE__ . '-' . __LINE__;
        exit(0);
        
    }
    function setDebugTemplate($tpl_name) {
        echo "Template Lite: Unsupported " . __FILE__ . '-' . __LINE__;
        exit(0);
        
    }
    
    
}


?>