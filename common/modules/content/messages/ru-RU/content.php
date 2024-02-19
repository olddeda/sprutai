<?php

return [

	// Titles
	'title' => 'Контент',

    // Blocks
    'block_catalog_items' => 'Устройства в материале',

	// Headers
	'header_general' => 'Основные данные',
	'header_author' => 'Данные автора',
	'header_other' => 'Дополнительные данные',
	'header_unique' => 'Антиплагиат',

	// Fields
	'field_id' => 'ID',
	'field_type' => 'Тип',
	'field_company_id' => 'Компания',
	'field_content_id' => 'Родительский ID',
	'field_author_id' => 'Автор',
	'field_title' => 'Название',
	'field_category' => 'Категория',
	'field_tag' => 'Теги',
	'field_descr' => 'Краткое описание',
	'field_text' => 'Текст',
	'field_date' => 'Дата',
	'field_datetime' => 'Дата и время',
	'field_layout' => 'Шаблон',
	'field_slug' => 'Идентификатор',
	'field_is_main' => 'На главной',
	'field_page_type' => 'Тип страницы',
	'field_page_path' => 'Шаблон',
	'field_source_name' => 'Имя источника',
	'field_source_url' => 'Ссылка на источник',
	'field_pinned' => 'Закрепить вверху',
	'field_pinned_sequence' => 'Порядковый номер',
	'field_video_url' => 'Ссылка на Youtube ролик',
	'field_tags' => 'Теги',
	'field_tags_ids' => 'Теги',
    'field_payment_types_ids' => 'Типы платежей',
	'field_rating' => 'Рейтинг',
	'field_status' => 'Статус',
	'field_date_at' => 'Дата',
	'field_image' => 'Изображение',
	'field_video' => 'Видео',
	'field_created_by' => 'Создан пользователем',
	'field_updated_by' => 'Изменен пользователем',
	'field_created_at' => 'Дата создания',
	'field_updated_at' => 'Дата обновления',
	'field_published_at' => 'Дата публикации',
	'field_author_type' => 'Тип',
	'field_notification' => 'Уведомлять',
    'field_change_catalog_links' => 'Заменять ссылки на каталог',
	
	// Placeholders
	'placeholder_title' => 'Название',
	
	// Editable
	'editable_content_id' => 'Выберите новую родительский ID',
	'editable_title' => 'Введите новое название',
	'editable_slug' => 'Введите новый идентификатор',
	'editable_status' => 'Выберите новый статус',
	'editable_category' => 'Выберите новые категории',
	'editable_tag' => 'Выберите новые теги',
	'editable_is_main' => 'Выберите новое значение',
	'editable_user_id' => 'Выберите нового автора',
	
	// Buttons
	'button_autosave_original' => 'Загрузить оригинал',
	'button_autosave_own' => 'Загрузить свою копию',
	
	// Other
	'content_parent_none' => 'Без категории',
	'author_type_user' => 'Автор',
	'author_type_company' => 'Компания',
	
	// Modal
	'modal_moderate_warning_title' => 'Внимание!',
	'modal_moderate_warning_message' => 'Для отправки статьи на модерацию, Вам необходимо выполнить следующие требования:',
	'modal_moderate_warning_field_avatar' => 'Загрузить аватар профиля. <a href="{url}" target="_blank">[Изменить]</a>',
	'modal_moderate_warning_field_first_name' => 'Указать ваше имя. <a href="{url}" target="_blank">[Изменить]</a>',
	'modal_moderate_warning_field_last_name' => 'Указать вашу фамилию. <a href="{url}" target="_blank">[Изменить]</a>',
	'modal_moderate_warning_field_address' => 'Указать вашу страну и город. <a href="{url}" target="_blank">[Изменить]</a>',
	'modal_moderate_warning_field_telegram' => 'Подключить телеграмм к аккаунту. <a href="{url}" target="_blank">[Прочитать о подключении]</a>',
	
	// Messages
	'message_empty_tags' => 'Вы пытаетесь опубликовать без тегов.<br>Вы действительно хотите продолжить?',
	'message_empty_special_tag' => 'Вы пытаетесь опубликовать <b>без специального тега</b>.<br>Вы действительно хотите продолжить?',

    // Errors
    'error_not_exists' => 'Материал не найден или был удален',
    'error_moderated' => 'Материал находится на модерации и его редактировать нельзя',
];