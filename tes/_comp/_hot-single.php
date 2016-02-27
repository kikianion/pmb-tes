<?php
if(!isset($hot_search_field)) $hot_search_field=false;
?>

<?php
if(isset($custom_hot_toolbar)){
    echo $custom_hot_toolbar;
}
?>

<div class="hot-toolbar pull-right" style="margin-bottom: 3px;">
    <div class="form-inline" action="#">
        <?php
        if(isset($hot_btn_add_new)) {
            $hot_btn_add_new=false;
            ?>
            <button class="form-control btn btn-default" id="btn-hot-goto-last" title='Tambah record baru di akhir' >
                <span class="glyphicon glyphicon-triangle-bottom"></span>                
            </button>
            <?php    
        }

        if(isset($hot_btn_xls)) {
            $hot_btn_xls=false;
            ?>
            <button name="export" class="btn btn-default form-control" id='export-htable' title='' ><img src="../../images/xls.jpg" width=20 height=20/></button>

            <?php
        }
        ?>
        <button name="load" class="btn btn-default form-control" id='load-htable' title='Muat ulang data tabel' ><span class="glyphicon glyphicon-refresh"></span></button>

        <div class="input-group ">
            <input id="search_field" type="search" placeholder="Cari" class="form-control " />
            <span class="input-group-btn">
                <button class="btn btn-default form-control" type="button" id="clear-search" title='Bersihkan pencarian' >
                    <span class="glyphicon glyphicon-remove-circle"></span>
                </button>
            </span>
        </div>
    </div>
</div>
<div style="height: auto; width: 100%; border: 1px solid #aaaadd; overflow: hidden;">
    <div id="htable" style="overflow: hidden; height:600px; width: 100%; "></div>
</div>

<script type="text/javascript">
    var myData;
    var hot;

    //common function
    function load_htable_data(){
        $("#search_field").val('');
        load_hot_data(callback_success,callback_error,null);
    }

    function build_hot(on_before_build_table, on_after_build_table){

        $.extend(hot_options,hot_common_options);

        if(on_before_build_table!==undefined){
            on_before_build_table(hot_options);
        }

        $("#htable").handsontable(hot_options);
        hot=$("#htable").handsontable("getInstance");

        $("#load-htable").on("click",function(){
            load_htable_data();            
        });

        $("#clear-search").on("click",function(){
            $("#search_field").val('');
            handleTableFilter('');
        });

        $('#search_field').on('keyup',function(event){
            handleTableFilter(this.value);
            //for handlind disaigment of handsontablecolumn 2x call
            handleTableFilter(this.value);
        });

        $("#btn-hot-goto-last").on("click", function(){
            //console.log("last goto");
            var max_rows=$('#htable').handsontable('countRows');
            $('#htable').handsontable('selectCell', max_rows-1, 1, max_rows-1, 1, scrollToSelection = true);
        });

        if(on_after_build_table!==undefined){
            on_after_build_table(hot);
        }
    }

    function hot_on_after_change(change, source){
        //common
        //skip jika dari event sendiri
        if (source === 'loadData' || source=='self') {
            return;
        }

        //common
        //jika perubahan single
        if(change.length==1){
            var s1=castStringEmpty(change[0][2]);
            var s2=castStringEmpty(change[0][3]);

            //skip jika perubahan sama 
            if(s1==s2) return;
        }

        //transform info
        //containInsertNew=true jika ada row dengan id kosong, yang nantinya akan dproses query insert
        var transform_info=
        {
            containInsertNew: false,
        };

        //mengubah format cell menajdi rows sblm di kirim ke backend
        var transformed2rows=hot_transform_change_cell2row(hot, change, transform_info);

        //jika ad data yang perlu di sql insert, block hot supaya tidak bisa entry 
        if(transform_info.containInsertNew){
            hot.deselectCell();
            //blockDiv("#htable","",true);
        }

        //common
        //send ajax to ds
        $.ajax({
            url: '../ds/<?php echo $ds?>.php?ajax=1&f=save',
            dataType: 'json',
            type: 'POST',
            data: {changes: JSON.stringify(transformed2rows)}, // contains changed cells' data
            success: function (response) {
                //blockDiv("#htable","",false);

                if(response.result=='ok'){
                    flashMessage("Berhasil disimpan","success");
                }
                else{
                    if(response.msg.toLowerCase().indexOf('duplicate')>-1){
                        flashMessage("Gagal: duplikat data","warning");
                    }
                    else{
                        flashMessage(response.msg,"warning");
                    }
                }

                load_hot_data(
                    callback_success,
                    callback_error, 
                    function(){
                        var v1=$("#search_field").val();
                        handleTableFilter(''+v1);
                });

            },
            error: function(jqXHR, textStatus, errorThrown ){
                flashMessage("Gagal mengirim data","error");
            }
        });
    }

    /**
    * hot filter processing, value get from search textfield
    */
    function handleTableFilter(val){
        var value = ('' + val).toLowerCase(),row,col,r_len,c_len,td;
        var data = myData;
        var searcharray = [];
        if(value){
            for(row=0,r_len = data.length;row< r_len;row++){
                for(col=0,c_len = data[row].length;col < c_len; col++){
                    if(data[row][col] == null){
                        continue;
                    }
                    if(('' + data[row][col]).toLowerCase().indexOf(value) > -1){
                        searcharray.push(data[row]);
                        break;
                    }
                    else{
                    }
                }
            }
            hot.loadData(searcharray);
            hot.loadData(searcharray);
        }
        else{
            hot.loadData(myData);
        }
    }

    function hot_on_before_remove_row(index, amount){

        var data=[];
        for(i=0; i<amount; i++){
            rowID=hot.getDataAtCell(index+i, 0);
            data[i]=rowID;
        }

        BootstrapDialog.confirm({
            title: 'Peringatan',
            message: 'Apakah ingin menghapus data sebanyak '+data.length+'?',
            type: BootstrapDialog.TYPE_WARNING, // <-- Default value is BootstrapDialog.TYPE_PRIMARY
            closable: true, // <-- Default value is false
            draggable: true, // <-- Default value is false
            btnCancelLabel: 'Batal', // <-- Default value is 'Cancel',
            btnOKLabel: 'Hapus', // <-- Default value is 'OK',
            btnOKClass: 'btn-warning', // <-- If you didn't specify it, dialog type will be used,
            callback: function(result) {
                // result will be true if button was click, while it will be false if users close the dialog directly.
                if(result) {
                    flashMessage("Menghapus...","info");
                    $.ajax({
                        url: '../ds/<?php echo $ds?>.php?ajax=1&f=delete',
                        data: {'ids': data}, // contains changed cells' data
                        dataType: 'json',
                        type: 'POST',
                        success: function (res) {
                            if (res.result == 'ok') {
                                flashMessage("Berhasil","success");

                                load_hot_data(
                                    callback_success,
                                    callback_error, 
                                    function(){
                                        var v1=$("#search_field").val();
                                        handleTableFilter(''+v1);
                                });
                            }
                            else {
                                flashMessage("Gagal","error");
                            }
                        },
                        error: function () {
                            flashMessage("Gagal mengirim data","error");
                        }
                    });
                }
                else {
                    //                    load_hot_data(
                    //                        callback_success,
                    //                        callback_error, 
                    //                        function(){
                    //                            var v1=$("#search_field").val();
                    //                            handleTableFilter(''+v1);
                    //                   });
                }
            }
        });

    }
    var callback_success;
    var callback_error;
    function load_hot_data(callback_success_, callback_error_, callback1, custom_func_load){
        callback_success=callback_success_
        callback_error=callback_error_;
        showTopLoading(true);

        var f="";
        if(custom_func_load!=null) f=custom_func_load;
        else f="load";

        $.ajax({
            url: '../ds/<?php echo $ds?>.php?&ajax=1&f='+f,
            dataType: 'json',
            type: 'GET',
            success: function (res) {

                if(res.result=="ok"){
                    var data=[];
                    if(callback_success_!=null && typeof callback_success_==='function'){
                        data=callback_success_(res);
                    }
                    myData=data;

                    hot.loadData(data);

                }
                else{
                    flashMessage("error: "+res.msg,"error");
                }
                //eksekusi callback tambahn, untuk internal template
                if(callback1!=null && typeof callback1==='function'){
                    callback1();
                }
                showTopLoading(false);


            },
            error: function ( jqXHR, textStatus, errorThrown ){
                if(callback_error_!=null && typeof callback_error_==='function'){
                    callback_error_( textStatus, errorThrown );
                }

            }
        });
    }

    $(function(){
        var v1=$(window).innerHeight();

        //console.log(v1);
        $("#htable").css('height',v1-350);
    })
</script>