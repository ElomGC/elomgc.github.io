function newckeditor(name) {
    let rescorlist = ['#333333','#666666','#999999','#ffffff','#ffff00'],
        colorlist = ['753876','e30613','ff6600','00ff00','00aa88','337ab7','0000ff','6600ff','ff00ff'];
    $.each(colorlist,function (index,item){
        rescorlist = rescorlist.concat(colors(item));
    });
    InlineEditor.create( document.querySelector( `.${name}` ), {
            toolbar: {
                items: [
                    'heading',
                    'fontFamily',
                    'fontColor',
                    'fontBackgroundColor',
                    'fontSize',
                    '|',
                    'bold',
                    'italic',
                    'underline',
                    'strikethrough',
                    'removeFormat',
                    '|',
                    'alignment',
                    'link',
                    'bulletedList',
                    'numberedList',
                    'todoList',
                    '|',
                    'indent',
                    'outdent',
                    '|',
                    'imageUpload',
                    'CKFinder',
                    'blockQuote',
                    'insertTable',
                    '|',
                    'code',
                    'codeBlock',
                    'undo',
                    'redo'
                ]
            },
            language: 'zh-cn',
            fontSize: {
                options: [12,14,16,18,20,24,28,32,36],
                supportAllValues: true
            },
            fontColor: {
                colors: rescorlist,
                columns: 5,
            },
        fontBackgroundColor: {
                colors: rescorlist,
                columns: 5,
            },
            image: {
                toolbar: [
                    'imageTextAlternative',
                    'imageStyle:full',
                    'imageStyle:side'
                ]
            },
            ckfinder: {
                uploadUrl: '/api/upload/ckeditor.html',
                openerMethod: 'popup',
                options: {
                    resourceType: 'Images'
                }
            },
            table: {
                contentToolbar: [
                    'tableColumn',
                    'tableRow',
                    'mergeTableCells',
                    'tableProperties',
                    'tableCellProperties'
                ]
            },
            licenseKey: '',
        }).then( editor => {
            editor.model.document.on('change:data',function () {
                $(`#${name}`).val(editor.getData());
            });
            window.editor = editor;
        }).catch( error => {
            console.error( error );
    });
}
function colors(name = '000000') {
       let _add = [`#${name}`];
       for(let j = 1; j < 5; j++){
           let _name = '';
           for(let i = 0; i < 6; i++){
               let _n = name.substring(i,i + 1);
               _n = parseInt(_n,16);
               let _s = (16 - _n) / 5;
               _n = _n + _s * j;
               _name +=  parseInt(_n).toString(16);
           }
           _add.push('#' + _name);
       }
       return _add;
}