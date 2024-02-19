function jqueryTreei18n() {
	$.i18n().load({
		'ru-RU': {

			// Titles
			'title.create': 'Создание связи с тегом',
			'title.update': 'Редактирование связи с тегом',
			'title.remove': 'Удаление связи с тегом',
			'title.warning': 'Внимание!',

			// Buttons
			'button.add': 'Создать',
			'button.create': 'Создать',
			'button.update': 'Сохранить',
			'button.edit': 'Редактировать',
			'button.remove': 'Удалить',
			'button.close': 'Закрыть',

			// Fields
			'field.tag': 'Тег',

			// Placeholders
			'placeholder.tag': 'Выберите тег',

			// Confirms
			'confirm.delete': 'Вы действительно хотите удалить связь с тегом?',
			'confirm.delete.childs': 'Вы действительно хотите удалить связь с тегом которая так же содержит в себе другие связи?',

			// Messages
			'message.cannot.delete.root': 'Вы не можете удалить связь которая содержит в себе другие связи!',

			// Errors
			'error.tag.empty': 'Вы не выбрали тег',
		}
	});
}