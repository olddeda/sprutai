<?php

return [

	// Fields
	'field_id' => 'ID',
    'field_catalog_field_group_id' => 'Группа',
    'field_type' => 'Тип',
    'field_format' => 'Формат',
    'field_title' => 'Название',
    'field_identifier' => 'Индентификатор',
    'field_unit' => 'Единица измерения',
    'field_status' => 'Статус',
	'field_created_by' => 'Создан пользователем',
	'field_updated_by' => 'Изменен пользователем',
	'field_created_at' => 'Дата создания',
	'field_updated_at' => 'Дата обновления',

    // Errors
    'error_not_exists' => 'Поле не найдено или было удалено',
    'error_group_not_exists' => 'Группа полей с ID «{value}» не найдено',
    'error_field_not_exists' => 'Поле для «{attribute}» c ID «{value}» не найдено',
    'error_unique_title' => 'Поле с названием «{value}» уже существует',
    'error_unique_identifier' => 'Поле с идентификатором «{value}» уже существует',
    'error_invalid_identifier' => 'Поле «{attribute}» имеет неверный формат. Допускаются только латинские буквы, цифры и знак подчеркивания',
    'error_swap_not_equal_group' => 'Заменяемые поля должны быть в одной группе'
];