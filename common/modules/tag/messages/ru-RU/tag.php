<?php

return [
	
	// Titles
	'title' => 'Теги',
	'title_view' => 'Просмотр тега',
	'title_create' => 'Создание тега',
	'title_update' => 'Редактирование тега',
	'title_view_article' => 'Статьи по тегу {title}',
	'title_view_news' => 'Новости по тегу {title}',
	'title_view_blog' => 'Блоги по тегу {title}',
	'title_view_projects' => 'Проекты по тегу {title}',
	'title_view_plugins' => 'Плагины по тегу {title}',
	'title_view_author' => 'Авторы по тегу {title}',
	'title_view_companies' => 'Компании по тегу {title}',
	
	// Headers
	'header_general' => 'Основные данные',
	'header_links' => 'Связи',
	'header_other' => 'Дополнительные данные',
	'header_seo' => 'SEO',
	
	// Menu
	'menu_articles' => 'Статьи',
	'menu_news' => 'Новости',
	'menu_blogs' => 'Блоги',
	'menu_projects' => 'Проекты',
	'menu_plugins' => 'Плагины',
	'menu_authors' => 'Авторы',
	'menu_companies' => 'Компании',
	
	// Fields
	'field_id' => 'ID',
	'field_type' => 'Тип',
	'field_title' => 'Название',
	'field_descr' => 'Краткое описание',
	'field_text' => 'Текст',
	'field_telegram' => 'Никнейм telegram чата',
	'field_sequence' => 'Порядковый номер',
    'field_multiple' => 'Мультивыбор',
    'field_visible_preview' => 'Отображать в превью',
	'field_status' => 'Статус',
	'field_created_by' => 'Создан пользователем',
	'field_updated_by' => 'Изменен пользователем',
	'field_created_at' => 'Дата создания',
	'field_updated_at' => 'Дата обновления',
	'field_links_ids' => 'С другими тегами',
	'field_filters_ids' => 'С фильтрами',
    'field_catalog_field_group_ids' => 'С группами полей',
	
	// Editable
	'editable_type' => 'Выберите новый тип',
	'editable_title' => 'Введите новое название',
	'editable_status' => 'Выберите новый статус',
	
	// Confirms
	'confirm_delete' => 'Вы действительно хотите удалить этот тег?',
	'confirm_delete_name' => 'Вы действительно хотите удалить тег - <strong>«{title}»</strong>?',
	
	// Messages
	'message_create_success' => 'Тег был успешно создан',
	'message_update_success' => 'Тег был успешно обновлен',
	'message_delete_success' => 'Тег был успешно удален',
	
	// Errors
	'error_not_exists' => 'Тег не найден или был удален',
    'error_link_filter' => 'Нельзя прикреплять фильтр к чему либо',
    'error_link_filter_to_group' => 'Нельзя прекреплять к группе фильтров',
    'error_link_filter_to_filter' => 'Нельзя прекреплять к фильтру',
    'error_link_catalog_field_group' => 'Группы полей можно прикреплять только к типу устройств',
];