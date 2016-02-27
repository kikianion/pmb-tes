<div style="text-align: center; width: 100%;"><h3>Bank Soal</h3></div>

<?php
$ds="daftar-soal-ds";
$hot_btn_add_new=true;

$master_jenissoal=load_jenissoal();

?>

<style type="text/css">
.ui-dialog { z-index: 1000 !important ;}
</style>

<div style="padding: 10px;">
    <?php
    //hot compoonent template 
    include "_comp/_hot-single.php";
    ?>

</div>

<script type="text/javascript">

    var colMaps=[
        "-",//0
        "kodesoal",//1
        "jenis",//2
        "pertanyaan",//2
        "jwba",//2
        "jwbb",//2
        "jwbc",//2
        "jwbd",//2
        "jwbe",//2
        "kunci",//2
    ];

    var master_jenissoal=<?php echo json_encode($master_jenissoal)?>;

    var hot_options={
        colHeaders: [',', 'Kode Soal', 'Jenis','Pertanyaan','A','B','C','D','E','Kunci',
            ],
        colWidths: [30,80,100,450,200,200,200,200,200,50,
            100,100,100,100],
        columns: [
            {
                readOnly: true,
                renderer: colorRenderer,
            },
            {
                renderer: colorRenderer,
            },
            {
                editor: "select2",
                select2Options: {
                    data: master_jenissoal,
                    dropdownAutoWidth: true,
                    allowClear: true,
                    width: 'resolve',

                },
            },
            {
                renderer: htmlRenderer1,
                editor:'tinymce',
            },
            {
                renderer: htmlRenderer1,
                editor:'richTextModal',
            },
            {
                renderer: htmlRenderer1,
                editor:'richTextModal',
            },
            {
                renderer: htmlRenderer1,
                editor:'richTextModal',
            },
            {
                renderer: htmlRenderer1,
                editor:'richTextModal',
            },
            {
                renderer: htmlRenderer1,
                editor:'richTextModal',
            },
            {
                type:'autocomplete',
                strict: true,
                source: ['A','B','C','D','E'],
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

    $(function(){
        build_hot(
            function(hot_options){
                //hot_options.minSpareRows=10;
                //hot_options.minSpareCols=10;
            },
            function(hot){

        });

        $("#btn-hot-goto-last").on("click", function(){
            var max_rows=$('#htable').handsontable('countRows');
            max_rows=max_rows;
            $('#htable').handsontable('selectCell', max_rows-1, 1, max_rows-1, 1, scrollToSelection = true);
        });

        load_hot_data(
            function(res){
                //debugger;
                var data = [], row;

                for (var i = 0, ilen = res.data.length; i < ilen; i++) {
                    row = [];
                    row[0] = parseInt(res.data[i].id);
                    row[1] = res.data[i].kodesoal;
                    row[2] = res.data[i].jenis;
                    row[3] = res.data[i].pertanyaan;
                    row[4] = res.data[i].jwba;
                    row[5] = res.data[i].jwbb;
                    row[6] = res.data[i].jwbc;
                    row[7] = res.data[i].jwbd;
                    row[8] = res.data[i].jwbe;
                    row[9] = res.data[i].kunci;


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

<script type="text/javascript">

    function excel_build_header(sheet){
        var owner='<?php echo $ownerName?>';

        var header=[   // 10 col
            [{value:''+owner, metadata:{style:eb_format_left_bold.id}}],
            [{value:'Nilai Tes Penerimaan Mahasiswa Baru ', metadata:{style:eb_format_left_bold.id}}],
            ['Nomor','Username','No Peserta','Nama Lengkap', 'Prodi', 'Nilai'],
            ['(1)','(2)','(3)','(4)','(5)','(6)'],

        ];

        for(var i=0; i<header.length; i++){
            for(var j=0; j<header[i].length; j++){
                if(!header[i][j].metadata){
                    header[i][j]={value: header[i][j], metadata: {style:eb_format_center_bold.id }};
                }
            }
        }

        sheet.mergeCells('a1','f1');
        sheet.mergeCells('a2','f2');
        sheet.setColumns([
            {width: 8},
            {width: 20},
            {width: 20},
            {width: 45},
            {width: 25, hidden: false},
            {width: 16},
        ]);

        sheet.setRowInstructions(0, {
            height: 50,
        });

        return header;
    }


    function eb_format_data_out(data_out){

        var flippedMap=array_flip_keyval(colMaps);

        //formating excel display by rule
        for(var i=0; i<data_out.length; i++){
            for(var j=0; j<data_out[i].length; j++){
                //                if ( j==0 && isNaN(data_out[i][0])) {
                //                  continue;
                //            }
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

                var current_bid="";
                for(var h=0; h<data_.length; h++){

                    if(current_bid!=data_[h][3]){
                        current_bid=data_[h][3];
                        if(current_bid==null) current_bid='NA';
                        data_out.push(['','','','',{value:'Prodi: '+current_bid, metadata:{style:eb_format_left_bold.id}}]);
                    }
                    data_out.push([h+1, data_[h][0], data_[h][1], data_[h][2], data_[h][3], data_[h][4] ]);

                }

                data_out=eb_format_data_out(data_out,true);

                var data_all=eb_header_arr.concat(data_out);

                sheet1.setData(data_all); 

                book.addWorksheet(sheet1);                    

                var filebin=builder.createFile(book);

                var fileName="NIlai-pmb-"+nowDT()+".xlsx";
                eb_download(filebin, fileName);
        });
    }


</script>


<?php
function load_jenissoal(){
    global $pdoc;
    $s="select kode as id, namajenis as text from jenis";
    $st=$pdoc->prepare($s);
    $st->execute();

    return $st->fetchAll(PDO::FETCH_ASSOC);
}
?>
