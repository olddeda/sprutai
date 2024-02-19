<?php

return [

	// Titles
	'title' => 'Комментарии',
	'index_title' => 'Список комментариев',
	'update_title' => 'Редактирование комментария',
	'statistics_title' => 'Статистика',

	// Headers
	'header_general' => 'Основные данные',
	'header_other' => 'Дополнительные данные',

	// Fields
	'field_id' => 'ID',
	'field_module_type' => 'Модуль',
	'field_related_to' => 'Название',
	'field_content' => 'Комментарий',
	'field_status' => 'Статус',
	'field_created_by' => 'Создан пользователем',
	'field_updated_by' => 'Изменен пользователем',
	'field_created_at' => 'Дата создания',
	'field_updated_at' => 'Дата обновления',
	'field_author' => 'Автор',

	// Fields placeholders
	'field_content_placeholder' => 'Напишите свой комментарий...',

	// Buttons
	'button_update' => 'Редактировать',
	'button_delete' => 'Удалить',
	'button_reply' => 'Ответить',
	'button_comment_send' => 'Отправить',
	'button_comment_save' => 'Сохранить',
	'button_comment_reply_cancel' => 'Отменить',

	// Confirms
	'confirm_delete' => 'Вы действительно хотите удалить этот комментарий?',
	'confirm_delete_name' => 'Вы действительно хотите удалить комментарий - <strong>«{title}»</strong>?',

	// Messages
	'message_update_success' => 'Комментарий был успешно обновлен',
	'message_delete_success' => 'Комментарий был успешно удален',
	'message_delete_failed' => 'При удалении комментария произошла ошибка',
	'message_comment_was_deleted' => 'Комментарий был удален',
    'message_spam' => 'Вы уже недавно оставляли отзыв',

    // Errors
    'error_not_exists' => 'Комментарий не найден или пренадлежит не вам',

	// Other
	'related_to_text' => '<a href="{url}" target="_blank" data-pjax="0">{title}</a>',
	'updated_date' => 'отредактировано {date}',
	
	// States
	'state_sending' => 'Отправка...',
];