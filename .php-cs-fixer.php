<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude('vendor')
    ->exclude('bootstrap')
    ->exclude('storage')
    ->exclude('node_modules')
    ->name('*.php')
    ->notName('*.blade.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new PhpCsFixer\Config())
    ->setRules([
        '@PER-CS3x0' => true,
        // Не используем yoda стиль
        'yoda_style' => [
            'equal' => false,
            'identical' => false,
            'less_and_greater' => false,
        ],
        // Пустые тела классов пишем в 2 строчки
        'single_line_empty_body' => false,
        // Убеждаемся, что если параметры функции не умещаются в одну строку, то мы из запишем каждый в своей строчке
        'method_argument_space' => [
            'on_multiline' => 'ensure_fully_multiline',
            'keep_multiple_spaces_after_comma' => false,
            'attribute_placement' => 'standalone',
        ],
        // 1 строка одно выражение
        'no_multiple_statements_per_line' => true,
        // Пробелы в конкатенации
        'concat_space' => ['spacing' => 'one'],
        // Пробелы в приведении типов
        'cast_spaces' => ['space' => 'single'],
        // Нет бесполезным импортам
        'no_unused_imports' => true,
        // Соблюдаем порядок в импортах
        'ordered_imports' => [
            'imports_order' => [
                'class', 'function', 'const',
            ],
        ],
        // Импортируем всё
        'fully_qualified_strict_types' => ['import_symbols' => true],
        // Разделяем блоки импорта пустой строкой
        'blank_line_between_import_groups' => true,
        // Импорты без слеша в начале
        'no_leading_import_slash' => true,
        // Короткий синтаксис массивов
        'array_syntax' => ['syntax' => 'short'],
        // В PHPDoc используем импортированные названия классов
        'phpdoc_no_package' => true,
        // Выравниваем блок коммента вместе с кодом
        'phpdoc_indent' => true,
        // Удаляет лишние пустые строки после сводки и описания в PHPDoc
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        // Одна строка @var PHPDoc должна yt иметь лишних пробелов
        'phpdoc_single_line_var_spacing' => true,
        // PHPDoc должен начинаться и заканчиваться содержимым, исключая самую первую и последнюю строку докблоков
        'phpdoc_trim' => true,
        // Аннотации @var и @type классических свойств не должны содержать имя
        'phpdoc_var_without_name' => true,
        // Удаляем пустой пхп док
        'no_empty_phpdoc' => true,
        // Каждая строка многострочного комментария к документу должна иметь * и быть выровнена по первой строке
        'align_multiline_comment' => ['comment_type' => 'all_multiline'],
        // Удаляем следующие аннотации
        'general_phpdoc_annotation_remove' => ['annotations' => ['author'], 'case_sensitive' => false],
        // PHPDoc должен содержать @param для всех параметров для которых не прописан нативный тип
        'phpdoc_add_missing_param_annotation' => ['only_untyped' => true],
        // Для стандартных типов PHP в PHPDoc необходимо использовать нижний регистр
        'phpdoc_types' => ['groups' => ['simple', 'alias', 'meta']],
        // В аннотациях @var и @type тип и имя должны быть указаны в правильном порядке: тип $var_name
        'phpdoc_var_annotation_correct_order' => true,
        // Описания аннотаций PHPDoc не должны быть предложениями
        'phpdoc_annotation_without_dot' => true,
        // Сортируем типы в PHPDoc, null последний, union типы - порядок не важен
        'phpdoc_types_order' => [
            'case_sensitive' => false,
            'null_adjustment' => 'always_last',
            'sort_algorithm' => 'none',
        ],
        // Для констант и переменных PHPDoc однострочный, для методов - многострочный
        'phpdoc_line_span' => [
            'const' => 'single',
            'method' => 'multi',
            'property' => 'single',
        ],
        // Порядок сортировки тегов
        'phpdoc_order_by_value' => ['annotations' => ['covers', 'throws']],
        // Удаляет @access аннотацию
        'phpdoc_no_access' => true,
        // Аннотации в PHPDoc должны располагаться в определенной последовательности.
        'phpdoc_order' => ['order' => ['param', 'return', 'throws', 'dataProvider']],
        // Упорядочивает все аннотации @param в DocBlocks в соответствии с сигнатурой метода
        'phpdoc_param_order' => true,
        // Тип аннотаций @return методов, возвращающих ссылку на себя, должен быть в одном стиле
        'phpdoc_return_self_reference' => [
            'replacements' => [
                'this' => '$this',
                '@this' => '$this',
                '$self' => 'self',
                '@self' => 'self',
                '$static' => 'static',
                '@static' => 'static',
            ],
        ],
        // После PHPDoc нет пустых строк
        'no_blank_lines_after_phpdoc' => true,
        // После открывающей скобки класса не должно быть пустых строк
        'no_blank_lines_after_class_opening' => true,
        // Во всех возможных местах добавляем запятую в списки
        'trailing_comma_in_multiline' => [
            'elements' => ['arguments', 'arrays', 'match', 'parameters'],
        ],
        // Если список в строчку, то после последнего элемента запятая не нужна
        'no_trailing_comma_in_singleline' => ['elements' => ['arguments', 'array_destructuring', 'array', 'group_import']],
        // Запрет на более чем одну пустую строку
        // Еще есть: 'parenthesis_brace_block', 'return', 'square_brace_block', 'switch', 'throw', 'use',
        // 'default', 'curly_brace_block', 'continue', 'case', 'break', 'attribute',
        'no_extra_blank_lines' => [
            'tokens' => [
                'extra',
                'parenthesis_brace_block',
                'square_brace_block',
                'curly_brace_block',
                'default',
                'switch',
            ],
        ],
        // Одиночные кавычки используем
        'single_quote' => true,
        // Не пишем концевых пробелов в комментах
        'no_trailing_whitespace_in_comment' => true,
        // Тернарки форматируем с пробелами
        'ternary_operator_spaces' => true,
        // Убираем лишние пробелы в массивах
        'trim_array_spaces' => true,
        // Все бинарные операторы разделяем одним пробелом с обоих сторон
        // =, *, /, %, <, >, |, ^, +, -, &, &=, &&, ||, .=, /=, =>, ==, >=, ===, !=, <>, !==, <=,
        // and, or, xor, -=, %=, *=, |=, +=, <<, <<=, >>, >>=, ^=, **, **=, <=>, ??, ??=
        'binary_operator_spaces' => [
            'default' => 'single_space',
        ],
        // Любые операторы в случае переноса строк, стоят в строке на первом месте
        'operator_linebreak' => ['only_booleans' => false, 'position' => 'beginning'],
        // null false true прописные
        'constant_case' => ['case' => 'lower'],
        // else на той же строке, что и закрывающая скобка
        'control_structure_continuation_position' => ['position' => 'same_line'],
        // Выравнивание цепочек вызовов методов
        'method_chaining_indentation' => true,
        // Не используем <>
        'standardize_not_equals' => true,
        // Выравнивание в массивах, такое же как и в коде
        'array_indentation' => true,
        //Стираем лишние проблемы рядом с объявлениями типов
        'type_declaration_spaces' => ['elements' => ['function', 'property']],
        // Отделяем проблемами следующие операторы (еще хочу: 'break', 'continue', 'declare', 'throw', 'try')
        'blank_line_before_statement' => ['statements' => ['return', 'throw']],
        // Нет лишним пробелам в массивах
        'no_multiline_whitespace_around_double_arrow' => true,
        // Нет пробела перед запятой в массиве
        'no_whitespace_before_comma_in_array' => true,
        // Используем квадратные скобки
        'normalize_index_brace' => true,
        // После запятой всего 1 пробел
        'whitespace_after_comma_in_array' => ['ensure_single_space' => true],
        // Use && and || logical operators instead of 'and' and 'or'
        'logical_operators' => true,
        // Не используем is_null, кроме как в виде Callable функции
        'is_null' => true,
        // Используем <?=
        'echo_tag_syntax' => [
            'format' => 'short',
        ],
        // Проставляем пустые возвратные типы, убираем лишние возвраты
        'phpdoc_no_empty_return' => true,
        // Удаляем бессмысленный тег @inheritdoc если класс не наследуется
        'phpdoc_no_useless_inheritdoc' => true,
        // Удаляем избыточное из PHPDoc
        'no_superfluous_phpdoc_tags' => ['allow_mixed' => true, 'remove_inheritdoc' => true],
        // Пишем типы правильно
        'phpdoc_scalar' => true,
        // Удаляем бессмысленные возвраты
        'no_useless_return' => true,
        // Один импорт одна строка, без группировок
        'single_import_per_statement' => true,
        // Запрет группировки импортов
        'group_import' => false,
        // Импортируем всё из глобального неймспейса, никаких \ перед классами, константами и функциями
        'global_namespace_import' => ['import_classes' => true, 'import_constants' => true, 'import_functions' => true],
        // Переносим уехавшие точки с запятой в цепочных вызовах на последнюю строку
        'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
        // Разделять блоки PHPDoc пустой строкой, комментарий, параметры, возвращаемое значение, бросаемые ошибки
        // Копия настроек для symfony, добавка от себя return, throws
        'phpdoc_separation' => [
            'groups' => [
                ['Annotation', 'NamedArgumentConstructor', 'Target'],
                ['author', 'copyright', 'license'],
                ['property', 'property-read', 'property-write'],
                ['return', 'throws'],
                ['deprecated', 'link', 'see', 'since'],
                ['dataProvider'],
            ],
            'skip_unlisted_annotations' => true,
        ],
        // Аттрибуты без скобок
        'attribute_empty_parentheses' => ['use_parentheses' => false],
        // Запрет кирилицы в именах
        'no_homoglyph_names' => true,
        // Разделение элементов класса пустыми строками, для элементов: [метод]
        'class_attributes_separation' => [
            'elements' => [
                'method' => 'one',
            ],
        ],
        'return_assignment' => true,
        'void_return' => true,
        'nullable_type_declaration' => ['syntax' => 'question_mark'],
    ])
    ->setFinder($finder);
