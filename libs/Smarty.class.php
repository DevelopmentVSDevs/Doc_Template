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
        if (isset($this->_vars[$var])) return($this->_vars[$var]);
        else return(false);
    }
    
    
    /*
    
    function isCached($template, $cache_id = null, $compile_id = null) {
        
    }
    
    
    function createData($parent = null) {
    }
    function createTemplate($template, $cache_id = null, $compile_id = null, $parent = null) {
    }
    function enableSecurity() {
    }
    function disableSecurity() {
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
        echo "load plugin XXX";
    }
    
    function registerPlugin($type, $tag, $callback, $cacheable = true, $cache_attr = array()) {
        echo "registerPlugin XXX";
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
    
    /*
    function registerObject($object_name, $object_impl, $allowed = array(), $smarty_args = true, $block_methods = array()) {
    }
    */
    
    
    /*
    function setExceptionHandler($handler) {
    }
    */
    
    
    /*
    function assignGlobal($varname, $value = null, $nocache = false) {
    }
    */
    
    
    /*
    function getGlobal($varname = null) {
    }
    function getRegisteredObject($name) {
    }
    function getDebugTemplate() {
    }
    function setDebugTemplate($tpl_name) {
    }
    */
    
    
    
    function &getTemplateVars($varname = null, $_ptr = null, $search_parents = true) {
        // function &get_template_vars($key = null)
        if ($_ptr != null && $search_parents !== true) {
            echo "Template Lite: Unsupported " . __FILE__ . '-' . __LINE__;
            exit(0);
        }
        return (parent::get_template_vars($varname = null));
        
    }
    
    function clearAllCache($exp_time = null, $type = null) {
        echo "Template Lite: Unsupported " . __FILE__ . '-' . __LINE__;
        exit(0);
    }
    function clearCache($template_name, $cache_id = null, $compile_id = null, $exp_time = null, $type = null) {
        echo "Template Lite: Unsupported " . __FILE__ . '-' . __LINE__;
        exit(0);
    }
    
    
    /*
    function registerResource($resource_type, $function_names) {
    }
    function registerDefaultPluginHandler($function_name) {
    }
    function registerDefaultTemplateHandler($function_name) {
    }
    */
    
    function unregisterPlugin($type, $tag) {
    }
    /*
    function unregisterObject($object_name) {
    }
    */
    function unregisterFilter($type, $function_name) {
    }
    /*
    function unregisterResource($resource_type) {
    }
    */
    
    /*
    function compileAllTemplates($extention = '.tpl', $force_compile = false, $time_limit = 0, $max_errors = null) {
    }
    function clearCompiledTemplate($resource_name = null, $compile_id = null, $exp_time = null) {
    }
    */
    
    
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
    
    
    
    /*
    $smarty->register->*
    $smarty->unregister->*
    $smarty->utility->*
    $samrty->cache->*
    
    Haveallbeenchangedtolocalmethodcallssuch as:
        
        $smarty->clearAllCache() $smarty->registerFoo() $smarty->unregisterFoo() $smarty->testInstall()
        
        
        
        $smarty->registerFilter( . . .) $smarty->unregisterFilter( . . .)
        
        $smarty->registerPlugin( . . .) $smarty->unregisterPlugin( . . .) */
    }
    
    /*
    class Template_Lite_Compiler2 extends Template_Lite_Compiler {
    function Template_Lite_Compiler2() {
    
}


} */







?>