(function($) {
    $.fn.tree = function(options) {
        var defaults = {
            'add_option': true,
            'edit_option': true,
            'delete_option': true,
            'view_option': false,
            'confirm_before_delete': true,
            'animate_option': [false, 5],
            'fullwidth_option': false,
            'align_option': 'center',
            'draggable_option': false,

            'url': {
                'search': '',
                'create': '',
                'update': '',
                'delete': ''
            },
            
            'callbacks': {
                'create': false,
                'update': false,
                'delete': false,
            }
        };

        jqueryTreei18n();

        var getcode = document.URL;

        return this.each(function() {
            if (options)
                $.extend(defaults, options);

            var self = $(this);
            var add_option = defaults['add_option'];
            var edit_option = defaults['edit_option'];
            var delete_option = defaults['delete_option'];
            var view_option = defaults['view_option'];
            var confirm_before_delete = defaults['confirm_before_delete'];
            var animate_option = defaults['animate_option'];
            var fullwidth_option = defaults['fullwidth_option'];
            var align_option = defaults['align_option'];
            var draggable_option = defaults['draggable_option'];
            
            var callback_create = defaults['callbacks']['create'];
            var callback_update = defaults['callbacks']['update'];
            var callback_delete = defaults['callbacks']['delete'];
            
            var url_search = defaults['url']['search'];
            var url_create = defaults['url']['create'];
            var url_update = defaults['url']['update'];
            var url_delete = defaults['url']['delete'];

            var vertical_line_text = '<span class="vertical"></span>';
            var horizontal_line_text = '<span class="horizontal"></span>';
            var add_action_text = add_option == true ? '<span class="add_action" data-i18n-title="button.add"><i class="fa fa-plus" aria-hidden="true"></i></span>' : '';
            var edit_action_text = edit_option == true ? '<span class="edit_action" data-i18n-title="button.edit"><i class="fa fa-pencil" aria-hidden="true"></i></span>' : '';
            var delete_action_text = delete_option == true ? '<span class="delete_action" data-i18n-title="button.remove"><i class="fa fa-trash" aria-hidden="true"></i></span>' : '';
            var highlight_text = '<span class="highlight" title="Click for Highlight | dblClick"></span>';
            var class_name = $(this).attr('class');
            var event_name = 'pageload';

            if (align_option != 'center')
                $('.' + class_name + ' li').css({ 'text-align': align_option });
            
            if (fullwidth_option) {
                if (typeof(fullwidth_option) == 'boolean') {
                    var i = 0;
                    var prev_width;
                    var get_element;
                    $('.' + class_name + ' li li').each(function() {
                        var this_width = $(this).width();
                        if (i == 0 || this_width > prev_width) {
                            prev_width = $(this).width();
                            get_element = $(this);
                        }
                        i++;
                    });
                    var loop = get_element.closest('ul').children('li').eq(0).nextAll().length;
                    var fullwidth = parseInt(0);
                    for ($i = 0; $i <= loop; $i++) {
                        fullwidth += parseInt(get_element.closest('ul').children('li').eq($i).width());
                    }
                    $('.' + class_name + '').closest('div').width(fullwidth);
                }
                else if (typeof (fullwidth_option) == 'string') {
                    $('.' + class_name + '').closest('div').width(fullwidth_option);
                }
            }

            $('.' + class_name + ' li.thide').each(function() {
                $(this).children('ul').hide();
            });

            function prepend_data(target) {
                target.prepend(vertical_line_text + horizontal_line_text).children('div').prepend(add_action_text + delete_action_text + edit_action_text);
                if (target.children('ul').length != 0)
                    target.hasClass('thide') ? target.children('div').prepend('<b class="thide tshow"></b>') : target.children('div').prepend('<b class="thide"></b>');
                target.children('div').prepend(highlight_text);

                $('[data-i18n-title]', self).each(function () {
                    $(this).attr('title', $.i18n($(this).data('i18n-title')));
                });
            }

            function draw_line(target) {
                var tree_offset_left = $('.' + class_name + '').offset().left;
                tree_offset_left = parseInt(tree_offset_left, 10);
                var child_width = target.children('div').outerWidth(true) / 2;
                var child_left = target.children('div').offset().left;
                if (target.parents('li').offset() != null)
                    var parent_child_height = target.parents('li').offset().top;
                vertical_height = (target.offset().top - parent_child_height) - target.parents('li').children('div').outerHeight(true) / 2;
                target.children('span.vertical').css({ 'height': vertical_height, 'margin-top': -vertical_height, 'margin-left': child_width, 'left': child_left - tree_offset_left });
                if (target.parents('li').offset() == null) {
                    var width = 0;
                } else {
                    var parents_width = target.parents('li').children('div').offset().left + (target.parents('li').children('div').width() / 2);
                    var current_width = child_left + (target.children('div').width() / 2);
                    var width = parents_width - current_width;
                }
                var horizontal_left_margin = width < 0 ? -Math.abs(width) + child_width : child_width;
                target.children('span.horizontal').css({ 'width': Math.abs(width), 'margin-top': -vertical_height, 'margin-left': horizontal_left_margin, 'left': child_left - tree_offset_left });
            }

            if (animate_option[0] == true) {
                function animate_call_structure() {
                    $timeout = setInterval(function() {
                        animate_li();
                    }, animate_option[1]);
                }
                var length = $('.' + class_name + ' li').length;
                var i = 0;

                function animate_li() {
                    prepend_data($('.' + class_name + ' li').eq(i));
                    draw_line($('.' + class_name + ' li').eq(i));
                    i++;
                    if (i == length) {
                        i = 0;
                        clearInterval($timeout);
                    }
                }
            }

            function call_structure() {
                console.log('call');
                $('.' + class_name + ' li').each(function() {
                    if (event_name == 'pageload')
                        prepend_data($(this));
                    draw_line($(this));
                });
            }

            function clear_selection() {
                $('.' + class_name + ' li > div').each(function () {
                    $(this).children('span.highlight, span.add_action, span.delete_action, span.edit_action').hide();
                });
            }

            function find_parent(_this) {
                if (_this.length > 0) {
                    _this.children('div').addClass('parent');
                    _this = _this.closest('li').closest('ul').closest('li');
                    return find_parent(_this);
                }
            }

            function create_modal(isCreate, data) {
                var title = (isCreate) ? 'title.create' : 'title.update';
                var buttonSubmit = (isCreate) ? 'button.create' : 'button.update';

                var html = '' +
                    '<div id="modal-tree" class="modal fade">' +
                    '    <div class="modal-dialog">' +
                    '       <div class="modal-content">' +
                    '           <div class="modal-header">' +
                    '               <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>' +
                    '               <h4 class="modal-title" data-i18n="' + title + '"></h4>' +
                    '           </div>' +
                    '           <div class="modal-body">' +
                    '               <div class="form-group">' +
                    '                   <label class="control-label" data-i18n="field.tag" for="tree-tag"></label>' +
                    '                   <select id="tree-tag" class="form-control"></select>' +
                    '                   <p class="help-block help-block-error"></p>' +
                    '               </div>' +
                    '           </div>' +
                    '           <div class="modal-footer">' +
                    '               <button type="button" class="btn btn-submit btn-primary" data-i18n="' + buttonSubmit + '"></button>' +
                    '               <button type="button" class="btn btn-close btn-default" data-i18n="button.close" data-dismiss="modal"></button>' +
                    '           </div>' +
                    '       </div>' +
                    '   </div>' +
                    '</div>';
                var modal = $(html);
                $('body').append(modal);

                if (data) {
                    var option = $("<option selected='selected'></option>").val(data.id).text(data.title);
                    $('#tree-tag').append(option);
                }

                $('[data-i18n]', $('#modal-tree')).i18n();

                $('#modal-tree').modal('show').on('hidden.bs.modal', function() {
                    $(this).remove();
                });

                var select = $('#tree-tag');
                select.select2({
                    language: 'ru',
                    placeholder: $.i18n('placeholder.tag'),
                    ajax: {
                        url: url_search,
                        data: function (params) {
                            var query = {
                                q: params.term,
                                selected: data ? data.id : 0,
                            }
                            return query;
                        }
                    }
                }).on('select2:select', function (e) {
                    if (select.val()) {
                        select.closest('.form-group').removeClass('has-error').addClass('has-success');
                        select.closest('.form-group').find('.help-block').html('');
                    }
                });

                return modal;
            }

            function create_modal_alert(text) {
                var html = '' +
                    '<div id="modal-tree" class="modal fade">' +
                    '    <div class="modal-dialog">' +
                    '       <div class="modal-content">' +
                    '           <div class="modal-header">' +
                    '               <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>' +
                    '               <h4 class="modal-title" data-i18n="title.warning"></h4>' +
                    '           </div>' +
                    '           <div class="modal-body">' +
                    '               <p>' + text + '</p>' +
                    '           </div>' +
                    '           <div class="modal-footer">' +
                    '               <button type="button" class="btn btn-default" data-i18n="button.close" data-dismiss="modal"></button>' +
                    '           </div>' +
                    '       </div>' +
                    '   </div>' +
                    '</div>';
                $('body').append(html);

                $('[data-i18n]', $('#modal-tree')).i18n();

                $('#modal-tree').modal('show').on('hidden.bs.modal', function() {
                    $(this).remove();
                });
            }

            animate_option[0] ? animate_call_structure() : call_structure();

            event_name = 'others';

            $(window).resize(function() {
                call_structure();
            });
            
            setTimeout(() => {
                call_structure();
                self.animate({opacity: 1});
            }, 500)
            
            if (!view_option) {
                $(document).on("click", 'body', function(event) {
                    var allowClear = true;
                    if ($(event.target).hasClass('node'))
                        allowClear = false;
                    if ($(event.target).closest('.node').length)
                        allowClear = false;
                    if ($(event.target).closest('.modal').length)
                        allowClear = false;
                    if (allowClear) {
                        clear_selection();
                    }
                });
                
                $(document).on("click", '.' + class_name + ' b.thide', function() {
        
                    $(this).toggleClass('tshow');
                    $(this).closest('li').toggleClass('thide').children('ul').toggle();
                    call_structure();
                });
    
                $(document).on("click", '.' + class_name + ' li > div', function(event) {
                    var objLevel = parseInt($(this).attr('level'));
        
                    clear_selection();
        
                    $('.' + class_name + ' li > div.current').removeClass('current');
                    $('.' + class_name + ' li > div.children').removeClass('children');
                    $('.' + class_name + ' li > div.parent').removeClass('parent');
        
                    $(this).addClass('current');
        
                    $(this).closest('li').children('ul').children('li').children('div').addClass('children');
                    $(this).closest('li').closest('ul').closest('li').children('div').addClass('parent');
        
                    $(this).children('span.add_action').show();
                    if (objLevel)
                        $(this).children('span.add_action, span.delete_action, span.edit_action').show();
                });
            }

            if (add_option) {
                $(document).on("click", '.' + class_name + ' span.add_action', function() {
                    var obj = $(this).closest('div');
                    var objId = obj.attr('id');
                    
                    if (typeof(callback_create) == 'function') {
                        callback_create(objId);
                        return;
                    }
                    
                    var obj = $(this).closest('div');
                    var objId = obj.attr('id');

                    var modal = create_modal(true);

                    $('.btn-submit', modal).on('click', function() {
                        $(this).off('click');

                        var select = $('select', modal);
                        var tagId = select.val();

                        if (!tagId) {
                            select.closest('.form-group').addClass('has-error');
                            select.closest('.form-group').find('.help-block').html($.i18n('error.tag.empty'));
                            return;
                        }

                        var data = {
                            'nested_id': objId,
                            'tag_id': tagId,
                        };

                        $.ajax({
                            type: 'GET',
                            url: url_create,
                            data: data,
                            success: function(data) {
                                if (data.success) {
                                    var html = '' +
                                        '<li>' + vertical_line_text + horizontal_line_text +
                                        '   <div id="' + data.item.id + '" tag="' + data.item.tag_id + '" level="' + data.item.level + '">' + highlight_text + add_action_text + delete_action_text + edit_action_text + data.item.title + '</div>' +
                                        '</li>';
                                    obj.closest('li').children('ul').length > 0 ? obj.closest('li').children('ul').append(html) : obj.closest('li').append('<ul>' + html + '</ul>');
                                    call_structure();

                                    if (draggable_option)
                                        draggable_event();

                                    $('#modal-tree').modal('hide');
                                }
                                else {
                                    select.closest('.form-group').addClass('has-error');
                                    select.closest('.form-group').find('.help-block').html(data.error);
                                }
                            }
                        });
                    });
                });
            }

            if (edit_option) {
                $(document).on("click", '.' + class_name + ' span.edit_action', function() {
                    var obj = $(this).closest('div');
                    var objId = obj.attr('id');
                    var objTagId = obj.attr('tag');
                    var objTitle = obj.text();
    
                    if (typeof(callback_update) == 'function') {
                        callback_update(objId, objTagId);
                        return;
                    }

                    var modal = create_modal(false, {id: objTagId, title: objTitle});

                    $('.btn-submit', modal).on('click', function() {
                        $(this).off('click');

                        var select = $('select', modal);
                        var tagId = select.val();

                        if (!tagId) {
                            select.closest('.form-group').addClass('has-error');
                            select.closest('.form-group').find('.help-block').html($.i18n('error.tag.empty'));
                            return;
                        }

                        var data = {
                            'nested_id': objId,
                            'tag_id': tagId,
                        };

                        $.ajax({
                            type: 'GET',
                            url: url_update,
                            data: data,
                            success: function(data) {
                                if (data.success) {
                                    obj.attr('id', data.item.id);
                                    obj.html(data.item.title);

                                    call_structure();

                                    if (draggable_option)
                                        draggable_event();

                                    $('#modal-tree').modal('hide');
                                }
                                else {
                                    select.closest('.form-group').addClass('has-error');
                                    select.closest('.form-group').find('.help-block').html(data.error);
                                }
                            }
                        });
                    });
                });
            }

            if (delete_option) {
                $(document).on("click", '.' + class_name + ' span.delete_action', function() {
                    if ($(this).closest('div').attr('id') == 1) {
                        create_modal_alert($.i18n('message.cannot.delete.root'));
                        return;
                    }

                    var obj = $(this);
                    var objId = $(this).closest('div').attr('id');
                    var objTagId = $(this).closest('div').attr('tag');
                    var target_element = $(this).closest('li').closest('ul').closest('li');

                    var has_child = $(this).closest('li').children('ul').length !== 0;
                    var message = (has_child) ? $.i18n('confirm.delete.childs') : $.i18n('confirm.delete');

                    if (confirm_before_delete) {
                        yii.confirm(message, function() {
                            confirm_delete();
                        });
                    }
                    else {
                        confirm_delete();
                    }

                    function confirm_delete() {
                        if (typeof(callback_delete) == 'function') {
                            callback_delete(objId, objTagId);
                            return;
                        }
                        
                        
                        var ids = [];
                        ids.push(obj.closest('div').attr('id'));
                        obj.closest('li').find('li').each(function() {
                            ids.push($(this).children('div').attr('id'));
                        });

                        var data = {
                            'ids': ids,
                        };

                        $.ajax({
                            type: 'POST',
                            url: url_delete,
                            data: data,
                            success: function(data) {
                                if (data.success) {
                                    obj.closest('li').fadeOut().remove();

                                    call_structure();

                                    if (target_element.children('ul').children('li').length == 0)
                                        target_element.children('ul').remove();
                                }
                                else {
                                    select.closest('.form-group').addClass('has-error');
                                    select.closest('.form-group').find('.help-block').html(data.error);
                                }
                            }
                        });
                    }
                });
            }

            if (draggable_option) {
                function draggable_event() {
                    droppable_event();
                    $('.' + class_name + ' li > div').draggable({
                        cursor: 'move',
                        distance: 40,
                        zIndex: 5,
                        revert: true,
                        revertDuration: 100,
                        snap: '.tree li div',
                        snapMode: 'inner',
                        start: function(event, ui) {
                            $('li.li_children').removeClass('li_children');
                            $(this).closest('li').addClass('li_children');
                        },
                        stop: function(event, ul) {
                            droppable_event();
                        }
                    });
                }

                function droppable_event() {
                    $('.' + class_name + ' li > div').droppable({
                        accept: '.tree li div',
                        drop: function(event, ui) {
                            $('div.check_div').removeClass('check_div');
                            $('.li_children div').addClass('check_div');
                            if ($(this).hasClass('check_div')) {
                                alert('Cant Move on Child Element.');
                            } else {
                                //                                var data = "action=drag&id=" + $(ui.draggable[0]).attr('id') + "&parentid=" + $(this).attr('id') + "";
                                //                                $.ajax({
                                //                                    type: 'POST',
                                //                                    url: 'ajax.php',
                                //                                    data: data,
                                //                                    success: function(data) {
                                //                                    }
                                //                                });
                                $(this).next('ul').length == 0 ? $(this).after('<ul><li>' + $(ui.draggable[0]).attr({ 'style': '' }).closest('li').html() + '</li></ul>') : $(this).next('ul').append('<li>' + $(ui.draggable[0]).attr({ 'style': '' }).closest('li').html() + '</li>');
                                $(ui.draggable[0]).closest('ul').children('li').length == 1 ? $(ui.draggable[0]).closest('ul').remove() : $(ui.draggable[0]).closest('li').remove();
                                call_structure();
                                draggable_event();
                            }
                        }
                    });
                }
                //$('.' + class_name + ' li > div').disableSelection();
                draggable_event();
            }
        });
    };
})(jQuery);
