var isLoaded = false;

var codeMirrorLanguages = {
    'shell': 'Shell',
    'javascript': 'JavaScript',
    'yaml': 'YAML',
    'http': 'HTTP',
    'json': 'JSON',
    'xml': 'XML',
    'sql': 'SQL',
    'ruby': 'Ruby',
    'python': 'Python',
    'lua': 'Lua',
    'markdown': 'Markdown',
    //'perl': 'Perl',
    //'php': 'PHP',
};

CodeMirror.defaults = {
    //closeTag: false
}

var codeMirror;

function contentBuilderSave() {
    var obj = $('.contentbuilder-content');
    obj.saveimages({
        handler: 'media/upload',
        onComplete: function () {

            // Get content
            var sHTML = obj.data('contentbuilder').html();

            //Save content
            console.log(sHTML);

        }
    });
    obj.data('saveimages').save();
}

function onContentBuilderRender() {
    $(document).ready(function () {
        if (!isLoaded) {
            isLoaded = true;

            initCodeMirrorEditor();
            initCodeMirrorHighlight();
            contentbuilderLocalize();

            $('code.code').each(function () {
                onContentCodeMirror($(this));
            });
        }

        $('[data-i18n]').i18n();
        $('[data-i18n-title]').each(function () {
            $(this).attr('title', $.i18n($(this).data('i18n-title')));
        });

        $('#selSnips, #selCellBorderWidth, #selTableApplyTo, select#code-language').select2({
            minimumResultsForSearch: Infinity,
        });

        $('code.code').each(function () {
            var obj = $(this);
            if (!obj.hasClass('cm-s-default')) {
                onContentCodeMirror($(obj));
                codeMirrorHighlight($(obj));
            }
        });
    });
}

function onContentBuilderDrop(e, ui) {}

function onContentCodeMirror(obj) {
    var objContainer = obj.closest('.col-md-12');
    var tool = $('#divToolCodeSettings');
    var toolLink = $('#lnkCodeSettings');
    var parent = obj.closest('.ui-draggable');
    var modal = $('#md-code-mirror');
    var textarea = modal.find('textarea#code');
    var select = modal.find('select#code-language');

    parent.hover(function () {
        var offset = parent.offset();
        tool.css({
            top: offset.top + parent.height() / 2 - tool.height() / 2,
            left: offset.left + parent.width() / 2 - tool.width() / 2,
        }).stop(true, true).css({
            display: 'none'
        }).fadeIn(0);

        toolLink.unbind('click').bind('click', function (e) {
            e.preventDefault();

            tool.stop(true, true).fadeOut(0);

            modal.css('width', '50%');
            modal.simplemodal({
                isModal: true,
            });
            modal.data('simplemodal').show();

            var lang = obj.data('code-language');
            if (lang == 'json')
                lang = 'javascript';

            codeMirror.setValue(obj.data('code-source'));
            codeMirror.setOption('mode', lang);
            codeMirror.refresh();

            select.val(obj.data('code-language')).change();
        });

        $('#btnCodeMirrorEditorCancel').unbind('click').bind('click', function(e) {
            modal.data('simplemodal').hide();
        });

        $('#btnCodeMirrorEditorOk').unbind('click').bind('click', function(e) {
            modal.data('simplemodal').hide();

            var code = codeMirror.getValue();
            console.log('code', code);

            var lang = select.val();

            var newObj = $('<pre><code class="code language-' + lang + '"></code></pre>');
            newObj.find('code').text(code);
            objContainer.html(newObj);

            var newCode = newObj.find('code');

            codeMirrorHighlight(newCode);
            onContentCodeMirror(newCode);
        });

    }, function () {
        tool.stop(true, true).fadeOut(0);
    });
}

function initCodeMirrorEditor() {
    var htmlCodeMirrorEditorTool = '' +
        '<div id="divToolCodeSettings">' +
        '   <i id="lnkCodeSettings" class="cb-icon-link"></i>' +
        '</div>';
    $(htmlCodeMirrorEditorTool).hover(function () {
        $(this).stop(true, true).fadeIn(0);
    }, function () {}).appendTo($('#divCb'));

    var htmlCodeMirrorEditor = '' +
        '<div class="md-modal md-draggable" id="md-code-mirror">' +
        '   <div class="md-content">' +
        '       <div class="md-body">' +
        '           <div style="cursor: move;" class="md-modal-handle ui-draggable-handle"><i class="cb-icon-dot"></i><i class="cb-icon-cancel md-modal-close"></i></div>' +
        '           <select id="code-language"></select>' +
        '           <textarea id="code-mirror-code" name="code" style="height:450px;"></textarea>' +
        '       </div>' +
        '       <div class="md-footer">' +
        '           <button id="btnCodeMirrorEditorCancel" class="secondary" data-i18n="contentbuilder.button.cancel">Cancel</button>' +
        '           <button id="btnCodeMirrorEditorOk" class="primary" data-i18n="contentbuilder.button.ok">Ok</button>' +
        '       </div>' +
        '   </div>' +
        '</div>';
    $(htmlCodeMirrorEditor).appendTo($('#divCb'));

    codeMirror = CodeMirror.fromTextArea($('#code-mirror-code')[0], {
        lineNumbers: true,
	    autoCloseTags: true,
        matchBrackets: true,
        lint: true,
    });

    var select = $('#code-language');
    $.each(codeMirrorLanguages, (function(key, val) {
        $('<option></option>').text(val).val(key).appendTo(select);
    }));
    select.change(function() {
        var lang = $(this).val();
        if (lang == 'json')
            lang = 'javascript';
        codeMirror.setOption('mode', lang);
    });
}

function initCodeMirrorHighlight() {
    $('code.code').each(function () {
        codeMirrorHighlight($(this));
    });
}

function codeMirrorHighlight(obj) {
    var lang = obj.attr('class').replace('code', '').replace('language-', '').trim();
    var code = _.unescape(obj.html());

    obj.data('code-language', lang);
    obj.data('code-source', code);

    obj.empty().removeClass('cm-s-default').addClass('cm-s-default');

    var mode = lang
    if (mode == 'json')
        mode = 'javascript';

    console.log('code2', code);

    CodeMirror.runMode(code, mode, obj[0], {
	    autoCloseTags: true
    });

    $('<span></span>').addClass('code-mirror-language').html(lang).appendTo(obj.parent());

    $('<a></a>').attr('data-i18n', 'contentbuilder.copy.link').html($.i18n('contentbuilder.copy.link')).addClass('code-mirror-copy').appendTo(obj.parent()).click(function() {
        $this = $(this);

        var clip = new ClipboardJS('.code-mirror-copy', {
            'text': function() {
                return code;
            }
        });

        clip.on('success', function() {
            $this.html($.i18n('contentbuilder.copy.success'));

            setTimeout(function () {
                $this.html($.i18n('contentbuilder.copy.link'));
            }, 5000);
        });
        clip.on('error', function () {
            $this.text($.i18n('contentbuilder.copy.error', ''));

            setTimeout(function () {
                $this.html($.i18n('contentbuilder.copy.link'));
            }, 5000);
        });
    });
}

function contentBuilderImageSlim(img) {
    img.closest('.col-md-12').attr('contenteditable', false);

    if (!img.parent().hasClass('slim')) {
        console.log('no slim');

        var container = $('<div></div>');
        img.wrap(container);

        var inputFile = $('<input type="file" name="slim" />');
        img.after(inputFile);

        //img.remove();

        inputFile.slim();
    }
}

function codeMirrorClear(s) {
    var c = $('<div>').html(s);
    var $s = c.find('span[class*="cm-"]').contents().unwrap().end().end();
    $('[class*="code-"]', $s).remove();
    $('code', $s).removeClass('cm-s-default');
    return $s.html();
}