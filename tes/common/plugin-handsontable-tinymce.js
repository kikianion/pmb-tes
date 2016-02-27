/// select2 plugin
(function (Handsontable) {
    "use strict";

    var RichTextEditor = Handsontable.editors.TextEditor.prototype.extend();
    document.hotckeditor1_created=false;
    var hotparent;

    RichTextEditor.prototype.createElements = function () {
        // Call the original createElements method
        Handsontable.editors.TextEditor.prototype.createElements.apply(this, arguments);

        $("body").append("<div id='divtmceholder99'>"+
            "<textarea id='tmce99' class='handsontableInput'></textarea>"+

            "<input type='submit' tabindex='-1' style='position:absolute; top:-1000px'>"+
            "</div>"
        );

        var dialog1=$( "#divtmceholder99" ).dialog({
            autoOpen: false,
            height: 400,
            width: 650,
            xmodal: true,
            buttons: {
                "Batal": function(){
                    dialog1.dialog( "close" );
                },
                "Simpan": function() {
                    dialog1.dialog( "close" );
                    hotparent.instance.setDataAtCell(hotparent.row, hotparent.col, tinymce.get('tmce99').getContent());
                }
            },
            close: function() {
                //dialog1.dialog( "close" );            
            }
        });

       // debugger;
        //return;
        tinymce.init({
            height: 135,
            selector: '#tmce99',
            paste_data_images: true,
            plugins: [
                'advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker',
                'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
                'save table contextmenu colorpicker directionality emoticons template paste textcolor'
            ],
            toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons'  });


    };

    RichTextEditor.prototype.open = function() {
        hotparent=this;
        //Handsontable.editors.TextEditor.prototype.open.apply(this, arguments);

        var val=this.instance.getDataAtCell(this.row, this.col)+'';

        //debugger;
        tinymce.get('tmce99').setContent(val);

        $( "#divtmceholder99" ).dialog('open');
    };    

    RichTextEditor.prototype.finishEditing= function(isCancelled, ctrlDown) {
        // Remember to invoke parent's method
        arguments[0]=true;

        //console.log('revert:'+arguments[0]);
        Handsontable.editors.TextEditor.prototype.finishEditing.apply(this, arguments);

    };

    Handsontable.editors.RichTextEditor = RichTextEditor;
    Handsontable.editors.registerEditor('tinymce', RichTextEditor);

})(Handsontable);            
