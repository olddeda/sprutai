<?php

return [

    'order_create_subject' => 'Новый заказ №{id}',
    'order_create' =>
        '<b>Новый заказ</b>'.PHP_EOL.PHP_EOL.
        '<b>Устройство:</b> {catalog_item}'.PHP_EOL.
        '<b>Имя:</b> {fio}'.PHP_EOL.
        '<b>Телефон:</b> {phone}'.PHP_EOL.
        '<b>E-Mail:</b> {email}'.PHP_EOL.
        '<b>Статус:</b> {status}',

    'order_address_complete_subject' => 'Изменен заказ №{id} - заполнен адрес',
    'order_address_complete' =>
        '<b>Заполнен адрес</b>'.PHP_EOL.PHP_EOL.
        '<b>Устройство:</b> {catalog_item}'.PHP_EOL.
        '<b>Имя:</b> {fio}'.PHP_EOL.
        '<b>Телефон:</b> {phone}'.PHP_EOL.
        '<b>E-Mail:</b> {email}'.PHP_EOL.
        '<b>Адрес:</b> {address}'.PHP_EOL.
        '<b>Статус:</b> {status}',

    'order_address_request_subject' => 'Заказ стика {catalog_item} - требуется заполнение адреса',
    'order_address_request' =>
        '<b>Здравствуйте {fio}!</b>'.PHP_EOL.PHP_EOL.
        '<b>Данные вашего заказа</b>'.PHP_EOL.
        '<b>Номер заказа:</b> №{id}'.PHP_EOL.
        '<b>Устройство:</b> <a href="{catalog_item_url}">{catalog_item}</a>'.PHP_EOL.
        '<b>Имя:</b> {fio}'.PHP_EOL.
        '<b>Телефон:</b> {phone}'.PHP_EOL.
        '<b>E-Mail:</b> {email}'.PHP_EOL.
        '<b>Статус:</b> {status}'.PHP_EOL.PHP_EOL.
        'Для завершения оформления заказа, нам необходимо получить от вас адрес доставки.'.PHP_EOL.
        'Для этого вам нужно перейти по ссылке <a href="{url}">{url}</a> и заполнить адрес в нужном поле и потвердить его, нажав кнопку "Сохранить"'.PHP_EOL.PHP_EOL.
        'После завершения заполнения заказа ожидайте звонка от нас.'.PHP_EOL.PHP_EOL.
        '<b>С уважением,</b>'.PHP_EOL.'<b>команда Sprut.ai</b>',

    'order_preorder_subject' => 'Оформлен предзаказ на {catalog_item}',
    'order_preorder' =>
        '<b>Здравствуйте {fio}!</b>'.PHP_EOL.PHP_EOL.
        'Вы успешно оформили предзаказ! После старта продаж вы получите дополнительное письмо с описанием дальнейших действий.'.PHP_EOL.PHP_EOL.
        '<b>Данные вашего заказа</b>'.PHP_EOL.
        '<b>Номер заказа:</b> №{id}'.PHP_EOL.
        '<b>Устройство:</b> <a href="{catalog_item_url}">{catalog_item}</a>'.PHP_EOL.
        '<b>Имя:</b> {fio}'.PHP_EOL.
        '<b>Телефон:</b> {phone}'.PHP_EOL.
        '<b>E-Mail:</b> {email}'.PHP_EOL.PHP_EOL.
        'Ссылка на страницу с заказом - <a href="{order_link}">{order_link}</a>'.PHP_EOL.PHP_EOL.
        '<b>С уважением,</b>'.PHP_EOL.'<b>команда Sprut.ai</b>',
    'order_preorder_num' =>
        '<b>Здравствуйте {fio}!</b>'.PHP_EOL.PHP_EOL.
        'Вы успешно оформили предзаказ! После старта продаж вы получите дополнительное письмо с описанием дальнейших действий.'.PHP_EOL.
        'Ваш номер в очереди - <b>№{num}</b>'.PHP_EOL.PHP_EOL.
        '<b>Данные вашего заказа</b>'.PHP_EOL.
        '<b>Номер заказа:</b> №{id}'.PHP_EOL.
        '<b>Устройство:</b> <a href="{catalog_item_url}">{catalog_item}</a>'.PHP_EOL.
        '<b>Имя:</b> {fio}'.PHP_EOL.
        '<b>Телефон:</b> {phone}'.PHP_EOL.
        '<b>E-Mail:</b> {email}'.PHP_EOL.PHP_EOL.
        'Ссылка на страницу с заказом - <a href="{order_link}">{order_link}</a>'.PHP_EOL.PHP_EOL.
        '<b>С уважением,</b>'.PHP_EOL.'<b>команда Sprut.ai</b>'.PHP_EOL.PHP_EOL.
        '<i>Приносим свои извинения, если вы получили повторное письмо.</i>',

    'order_changed_subject' => 'Изменен статус заказа №{id} - {status}',
    'order_changed' =>
        '<b>Здравствуйте {fio}!</b>'.PHP_EOL.PHP_EOL.
        'Новый статус заказа - <b>{status}</b>'.PHP_EOL.PHP_EOL.
        '<b>Данные вашего заказа</b>'.PHP_EOL.
        '<b>Номер заказа:</b> №{id}'.PHP_EOL.
        '<b>Устройство:</b> <a href="{catalog_item_url}">{catalog_item}</a>'.PHP_EOL.
        '<b>Имя:</b> {fio}'.PHP_EOL.
        '<b>Телефон:</b> {phone}'.PHP_EOL.
        '<b>E-Mail:</b> {email}'.PHP_EOL.PHP_EOL.
        'Ссылка на страницу с заказом - <a href="{order_link}">{order_link}</a>'.PHP_EOL.PHP_EOL.
        '<b>С уважением,</b>'.PHP_EOL.'<b>команда Sprut.ai</b>',

    'order_changed_sent' =>
        '<b>Здравствуйте {fio}!</b>'.PHP_EOL.PHP_EOL.
        'Новый статус заказа - <b>{status}</b>'.PHP_EOL.PHP_EOL.
        'Служба доставки: <b><a href="{delivery_type_link}">{delivery_type}</a></b>'.PHP_EOL.
        '{delivery_link}'.PHP_EOL.PHP_EOL.
        '<b>Данные вашего заказа</b>'.PHP_EOL.
        '<b>Номер заказа:</b> №{id}'.PHP_EOL.
        '<b>Устройство:</b> <a href="{catalog_item_url}">{catalog_item}</a>'.PHP_EOL.
        '<b>Имя:</b> {fio}'.PHP_EOL.
        '<b>Телефон:</b> {phone}'.PHP_EOL.
        '<b>E-Mail:</b> {email}'.PHP_EOL.PHP_EOL.
        'Ссылка на страницу с заказом - <a href="{order_link}">{order_link}</a>'.PHP_EOL.PHP_EOL.
        '<b>С уважением,</b>'.PHP_EOL.'<b>команда Sprut.ai</b>',

    'order_changed_set_link' => 'Отследить статус вы можете на сайте службы доставки по ссылке <a href="{link}">{link}</a> введя код отслеживания {code}',
    'order_changed_set_integral' => 'Статус доставки вы можете узнать телефону <b>+7 (495) 150-13-31</b> назвав код отслеживания <b>{code}</b>',

    // License
    'order_license_subject' => 'Лицензия {type} на Sprut.Hub',
    'order_license' =>
        '<b>Здравствуйте {fio}!</b>'.PHP_EOL.PHP_EOL.
        '<b>Тип лицензии</b> {type}'.PHP_EOL.
        '<b>Код лицензии:</b> {code}'.PHP_EOL.PHP_EOL.
        'Пожалуйста никому не сообщайте и не передавайте код лицензии.'.PHP_EOL.PHP_EOL.
        'Всю информацию по установке, настройке программного обеспечения вы можете получить в нашем чате поддержки <a href="{support_chat_url}">{support_chat_title}</a>'.PHP_EOL.
        'Так же не забудьте посетить Wiki <a href="https://wiki.sprut.ai/ru/spruthub">https://wiki.sprut.ai/ru/spruthub</a>'.PHP_EOL.PHP_EOL.
        '<b>С уважением,</b>'.PHP_EOL.'<b>команда Sprut.ai</b>',

];