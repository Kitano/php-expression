PHP Expression Language
=======================

The purpose of this library is to provide a common base for an PHP Expression Language.

This is not really a creative library since it burrows almost all the code from the great
[JMSSecurityExtraBundle](http://jmsyst.com/bundles/JMSSecurityExtraBundle "") which already defines
more or less such a base for a powerful EL (Expression Language).

The idea is to take this code outside of the Johannes's bundle and to standardize it.

Adding a custom function compiler
---------------------------------

The isNumber() function expression:

1. First you need to create a compiler for your function
    
    ```PHP
    <?php
    
    namespace My\Expression\Compiler\Func;
    
    use Pel\Expression\Compiler\Func\FunctionCompilerInterface;
    use Pel\Expression\ExpressionCompiler;
    use Pel\Expression\Ast\FunctionExpression;
    use Pel\Exception\RuntimeException;
    
    class IsNumberFunctionCompiler implements FunctionCompilerInterface
    {
        public function getName()
        {
            return 'isNumber';
        }
    
        public function compilePreconditions(ExpressionCompiler $compiler, FunctionExpression $function)
        {
            if (1 !== count($function->args)) {
                throw new RuntimeException(sprintf('The isNumber() function expects exactly one argument, but got "%s".', var_export($function->args, true)));
            }
        }
    
        public function compile(ExpressionCompiler $compiler, FunctionExpression $function)
        {
            $compiler
                ->write("is_numeric(")
                ->compileInternal($function->args[0])
                ->write(")")
            ;
        }
    }
    ```

2. Next, after having instanciated the `ExpressionCompiler`, you just need to register your custom function compiler

    ```PHP
    <?php
    
    $compiler = new ExpressionCompiler();
    $compiler->addFunctionCompiler(new IsNumberFunctionCompiler());
    
    $evaluator = eval($compiler->compileExpression(new Expression("isNumber('1234')")));
    var_dump(call_user_func($evaluator, array()));
    // bool(true)
    
    $evaluator = eval($compiler->compileExpression(new Expression("isNumber('1234abc')")));
    var_dump(call_user_func($evaluator, array()));
    // bool(false)
    ```


License
-------

This bundle is under the MIT license. See the complete license in library:

    LICENSE
