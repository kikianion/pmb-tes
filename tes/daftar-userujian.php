<div style="text-align: center; width: 100%;"><h3>User Peserta Tes </h3></div>

<?php
$ds="daftar-userujian-ds";
$hot_btn_add_new=1;
$hot_btn_xls=true;

$master_prodi=load_prodiname();

?>
<div style="padding: 10px;">
    <?php
    //hot compoonent template 
    include "_comp/_hot-single.php";
    ?>
    <pre>
        * Username setelah ditentukan dan dipakai dalam tes, tidak boleh dirubah
        * Bila dirubah setelah terpakai akan mengakibatkan nama tidak dikenal
    </pre>
</div>

<script type="text/javascript">

    var master_prodi=<?php echo json_encode($master_prodi)?>;
    var owner='<?php echo $ownerName?>';

    var hot_options={
        colHeaders: [ '', 'Username','Password','Level','No Peserta','Nama Lengkap','No Tlp','Asal Sekolah','Prodi','Sisa<br> Menit','Sisa<br> Detik','Tgl Update'],
        colWidths: [30,100,100,50,180,300,100,100,100,50,50,250],
        columns: [
            {
                readOnly: true,
                renderer: htmlRenderer1,
            },
            {
                renderer: colorRenderer,
            },
            {
                renderer: colorRenderer,
            },
            {
                readOnly: true,
                renderer: colorRenderer,
            },
            {
                readOnly: true,
                renderer: colorRenderer,
            },
            {
                readOnly: true,
                renderer: colorRenderer,
            },
            {
                readOnly: true,
                renderer: colorRenderer,
            },
            {
                readOnly: true,
                renderer: colorRenderer,
            },
            {
                readOnly: true,
                renderer: colorRenderer,
            },
            {
                renderer: colorRenderer,
            },
            {
                renderer: colorRenderer,
            },
            {
                readOnly: true,
                renderer: colorRenderer,
            },
        ],
        beforeRemoveRow: function(index, amount){
            hot_on_before_remove_row(index, amount);
            return false;
        },
        afterCreateRow: function(index,amount){
        },
        afterChange: function (change, source) {
            hot_on_after_change(change, source);
        }                
    }

    $("#export-htable").click(function(){
        //var data=!=null?hot.getData():;

        var maxrows=hot.countRows();
        var maxcols=hot.countCols();
        export_excel(hot.getData(0,0,maxrows,maxcols));        
    });

    $(function(){
        build_hot(
            function(hot){

        });

        load_hot_data(
            function(res){
                //debugger;
                var data = [], row;

                for (var i = 0, ilen = res.data.length; i < ilen; i++) {
                    row = [];
                    row[0] = parseInt(res.data[i].id);
                    row[1] = res.data[i].username;
                    row[2] = res.data[i].password;
                    row[3] = res.data[i].level;
                    row[4] = res.data[i].nopes;
                    row[5] = res.data[i].namalengkap;
                    row[6] = res.data[i].nohp;
                    row[7] = res.data[i].asal;
                    row[8] = res.data[i].prodi;
                    row[9] = res.data[i].sisawaktu;
                    row[10] = res.data[i].sisawaktus;
                    row[11] = res.data[i].dt_;

                    data[i] = row;
                }
                return data;

            },
            function(textStatus, errorThrown ){
                flashMessage(textStatus+": "+errorThrown);
            },
            null
        );

    });



</script>

<?php
function load_prodiname(){
    global $pdoc;
    $s="select * from prodi";
    $st=$pdoc->prepare($s);
    $st->execute();

    return $st->fetchAll(PDO::FETCH_ASSOC);
}
?>

<script type="text/javascript">

    function excel_build_header(sheet){
        var owner='<?php echo $ownerName?>';

        var header=[   

        ];

        sheet.setColumns([
            {width: 8},
            {width: 10},
            {width: 10},
            {width: 20},
            {width: 20, hidden: false},
        ]);

        for(var i=0; i<header.length; i++){
            for(var j=0; j<header[i].length; j++){
                if(!header[i][j].metadata){
                    header[i][j]={value: header[i][j], metadata: {style:eb_format_center_bold.id }};
                }
            }
        }

        return header;
    }


    function eb_format_data_out(data_out){

        //formating excel display by rule
        for(var i=0; i<data_out.length; i++){
            for(var j=0; j<data_out[i].length; j++){
                if(data_out[i][j]==null) {
                    data_out[i][j]='';
                }
                if(data_out[i][j]!=undefined && !data_out[i][j].metadata ){
                    data_out[i][j]={value: data_out[i][j], metadata: {style:eb_format_left.id }};
                }
            }
        }

        return data_out;
    }

    function export_excel(data){
        flashMessage("Mengekspor seluruh data ...","info");

        require(['../../libs/excel-builder.js-master/excel-builder', 
            ], function (builder) {

                var book = builder.createWorkbook();
                var stylesheet = book.getStyleSheet();

                create_excel_style(stylesheet);

                //data penampung
                var data_out=[];

                var sheet1 = book.createWorksheet({name: "Nilai"});   
                var eb_header_arr=excel_build_header(sheet1,true);
                //travers data
                var data_from=$.makeArray(data) ;

                //travers keg                
                var data_=alasql("select * from ? ",[data_from]) ;

                var mergeGroupBy=[];

                
                var current_bid="";
                var ct1=0;
                for(var h=0; h<data_.length; h++){

                    var taken='';
                    if( data_[h][5] && (data_[h][5]).length>0 ) taken='-XXX-TIKET SUDAH TERPAKAI-XXX-';
                    data_out.push( [h+1, owner] );
                    data_out.push( ['', 'Tiket Peserta Tes PMB'] );
                    data_out.push( [taken, 'Username:',data_[h][1], 'Password:',data_[h][2]] );
                    data_out.push( [] );
                    
                    sheet1.setRowInstructions([h*4+2], {
                        height: 50,
                    });
                    sheet1.setRowInstructions([h*4+3], {
                        height: 20,
                    });
                    sheet1.mergeCells('b'+(h*4+1),'e'+(h*4+1) );
                    sheet1.mergeCells('b'+(h*4+2),'e'+(h*4+2) );
                }

                data_out=eb_format_data_out(data_out,true);

                var data_all=eb_header_arr.concat(data_out);

                sheet1.setData(data_all); 

                book.addWorksheet(sheet1);                    

                var filebin=builder.createFile(book);

                var fileName="UserTes-pmb-"+nowDT()+".xlsx";
                eb_download(filebin, fileName);
        });
    }


</script>
