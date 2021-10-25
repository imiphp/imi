<?php

declare(strict_types=1);
/**
 * Smarty Internal Plugin Compile Special Smarty Variable
 * Compiles the special $smarty variables.
 *
 * @author     Uwe Tews
 */

/**
 * Smarty Internal Plugin Compile special Smarty Variable Class.
 */
class smarty_internal_compile_private_special_variable extends Smarty_Internal_CompileBase
{
    /**
     * Compiles code for the special $smarty variables.
     *
     * @param array                                 $args      array with attributes from parser
     * @param \Smarty_Internal_TemplateCompilerBase $compiler  compiler object
     * @param mixed                                 $parameter
     *
     * @return string compiled code
     *
     * @throws \SmartyCompilerException
     */
    public function compile($args, Smarty_Internal_TemplateCompilerBase $compiler, $parameter)
    {
        $_index = preg_split("/\]\[/", substr($parameter, 1, \strlen($parameter) - 2));
        $variable = strtolower($compiler->getId($_index[0]));
        // @phpstan-ignore-next-line
        if (false === $variable)
        {
            $compiler->trigger_template_error('special $Smarty variable name index can not be variable', null, true);
        }
        // @phpstan-ignore-next-line
        if (!isset($compiler->smarty->security_policy)
            || $compiler->smarty->security_policy->isTrustedSpecialSmartyVar($variable, $compiler)
        ) {
            switch ($variable) {
                case 'foreach':
                case 'section':
                    if (!isset(Smarty_Internal_TemplateCompilerBase::$_tag_objects[$variable]))
                    {
                        $class = 'Smarty_Internal_Compile_' . ucfirst($variable);
                        Smarty_Internal_TemplateCompilerBase::$_tag_objects[$variable] = new $class();
                    }

                    return Smarty_Internal_TemplateCompilerBase::$_tag_objects[$variable]->compileSpecialVariable(
                        [],
                        $compiler,
                        $_index
                    );
                case 'capture':
                    if (class_exists('Smarty_Internal_Compile_Capture'))
                    {
                        return Smarty_Internal_Compile_Capture::compileSpecialVariable([], $compiler, $_index);
                    }

                    return '';
                case 'now':
                    return 'time()';
                case 'cookies':
                    if (isset($compiler->smarty->security_policy)
                        && !$compiler->smarty->security_policy->allow_super_globals
                    ) {
                        $compiler->trigger_template_error('(secure mode) super globals not permitted');
                        break;
                    }
                    $compiled_ref = <<<'CODE'
Imi\RequestContext::get('request')->getCookieParams()
CODE;
                    break;
                case 'get':
                    // @phpstan-ignore-next-line
                    if (isset($compiler->smarty->security_policy) && !$compiler->smarty->security_policy->allow_rsuper_globals)
                    {
                        $compiler->trigger_template_error('(secure mode) super globals not permitted');
                        break;
                    }
                    $compiled_ref = <<<'CODE'
Imi\RequestContext::get('request')->get()
CODE;
                    break;
                case 'post':
                    // @phpstan-ignore-next-line
                    if (isset($compiler->smarty->security_policy) && !$compiler->smarty->security_policy->allow_rsuper_globals)
                    {
                        $compiler->trigger_template_error('(secure mode) super globals not permitted');
                        break;
                    }
                    $compiled_ref = <<<'CODE'
Imi\RequestContext::get('request')->post()
CODE;
                    break;
                case 'server':
                    // @phpstan-ignore-next-line
                    if (isset($compiler->smarty->security_policy) && !$compiler->smarty->security_policy->allow_rsuper_globals)
                    {
                        $compiler->trigger_template_error('(secure mode) super globals not permitted');
                        break;
                    }
                    $compiled_ref = <<<'CODE'
Imi\RequestContext::get('request')->getServerParams()
CODE;
                    break;
                case 'session':
                    // @phpstan-ignore-next-line
                    if (isset($compiler->smarty->security_policy) && !$compiler->smarty->security_policy->allow_rsuper_globals)
                    {
                        $compiler->trigger_template_error('(secure mode) super globals not permitted');
                        break;
                    }
                    $compiled_ref = <<<'CODE'
Imi\Server\Session\Session::get()
CODE;
                    break;
                case 'request':
                    // @phpstan-ignore-next-line
                    if (isset($compiler->smarty->security_policy) && !$compiler->smarty->security_policy->allow_rsuper_globals)
                    {
                        $compiler->trigger_template_error('(secure mode) super globals not permitted');
                        break;
                    }
                    $compiled_ref = <<<'CODE'
Imi\RequestContext::get('request')->request()
CODE;
                    break;
                case 'env':
                    break;
                case 'template':
                    return 'basename($_smarty_tpl->source->filepath)';
                case 'template_object':
                    return '$_smarty_tpl';
                case 'current_dir':
                    return 'dirname($_smarty_tpl->source->filepath)';
                case 'version':
                    return 'Smarty::SMARTY_VERSION';
                case 'const':
                    if (isset($compiler->smarty->security_policy)
                        && !$compiler->smarty->security_policy->allow_constants
                    ) {
                        $compiler->trigger_template_error('(secure mode) constants not permitted');
                        break;
                    }
                    if (!str_contains($_index[1], '$') && !str_contains($_index[1], '\''))
                    {
                        return "@constant('{$_index[1]}')";
                    }
                    else
                    {
                        return "@constant({$_index[1]})";
                    }
                // no break
                case 'config':
                    if (isset($_index[2]))
                    {
                        return "(is_array(\$tmp = \$_smarty_tpl->smarty->ext->configload->_getConfigVariable(\$_smarty_tpl, $_index[1])) ? \$tmp[$_index[2]] : null)";
                    }
                    else
                    {
                        return "\$_smarty_tpl->smarty->ext->configload->_getConfigVariable(\$_smarty_tpl, $_index[1])";
                    }
                // no break
                case 'ldelim':
                    return '$_smarty_tpl->smarty->left_delimiter';
                case 'rdelim':
                    return '$_smarty_tpl->smarty->right_delimiter';
                default:
                    $compiler->trigger_template_error('$smarty.' . trim($_index[0], "'") . ' is not defined');
                    break;
            }
            if (isset($_index[1]))
            {
                array_shift($_index);
                foreach ($_index as $_ind)
                {
                    // @phpstan-ignore-next-line
                    $compiled_ref = $compiled_ref . "[$_ind]";
                }
            }

            // @phpstan-ignore-next-line
            return $compiled_ref;
        }
    }
}
